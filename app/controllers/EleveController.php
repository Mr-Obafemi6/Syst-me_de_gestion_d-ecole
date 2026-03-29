<?php
// app/controllers/EleveController.php

require_once ROOT_PATH . '/app/core/Controller.php';
require_once ROOT_PATH . '/app/models/Eleve.php';
require_once ROOT_PATH . '/app/models/Classe.php';

class EleveController extends Controller {

    private Eleve  $eleveModel;
    private Classe $classeModel;

    public function __construct() {
        $this->eleveModel  = new Eleve();
        $this->classeModel = new Classe();
    }

    // ─────────────────────────────────────────
    // GET /eleves — Liste paginée
    // ─────────────────────────────────────────
    public function index(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF]);

        $page      = max(1, (int) $this->get('page', 1));
        $recherche = trim($this->get('q', ''));
        $classeId  = (int) $this->get('classe', 0);

        $pagination = $this->eleveModel->listerAvecClasse($page, $recherche, $classeId);
        $classes    = $this->classeModel->toutesLesClasses();

        $this->render('eleves/liste', [
            'title'      => 'Élèves',
            'pageTitle'  => 'Gestion des élèves',
            'user'       => AuthMiddleware::user(),
            'flash'      => $this->getFlash(),
            'pagination'  => $pagination,
            'classes'     => $classes,
            'csrf_token'  => $this->generateCsrfToken(),
        ]);
    }

    // ─────────────────────────────────────────
    // GET /eleves/fiche/{id}
    // ─────────────────────────────────────────
    public function fiche(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF, ROLE_PARENT]);

        $id    = (int) $param;
        $eleve = $this->eleveModel->ficheComplete($id);

        if (!$eleve) {
            $this->flash('error', 'Élève introuvable.');
            Router::redirect('eleves');
        }

        // Parent ne peut voir que son enfant
        if (AuthMiddleware::hasRole(ROLE_PARENT)) {
            $user = AuthMiddleware::user();
            if ($eleve['parent_id'] != $user['id']) {
                Router::redirect('eleves/fiche/' . $id);
            }
        }

        $this->render('eleves/fiche', [
            'title'      => $eleve['prenom'] . ' ' . $eleve['nom'],
            'pageTitle'  => 'Fiche élève',
            'user'       => AuthMiddleware::user(),
            'flash'      => $this->getFlash(),
            'eleve'      => $eleve,
            'csrf_token' => $this->generateCsrfToken(),
        ]);
    }

    // ─────────────────────────────────────────
    // GET /eleves/ajouter
    // ─────────────────────────────────────────
    public function ajouter(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);

        $classes = $this->classeModel->toutesLesClasses();

        $this->render('eleves/formulaire', [
            'title'      => 'Ajouter un élève',
            'pageTitle'  => 'Ajouter un élève',
            'user'       => AuthMiddleware::user(),
            'flash'      => null,
            'csrf_token' => $this->generateCsrfToken(),
            'classes'    => $classes,
            'eleve'      => null,
            'errors'     => [],
        ]);
    }

    // ─────────────────────────────────────────
    // POST /eleves/store
    // ─────────────────────────────────────────
    public function store(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);
        $this->requireMethod('POST');
        $this->validateCsrf();

        $data   = $this->collectFormData();
        $errors = $this->valider($data);

        if (!empty($errors)) {
            $classes = $this->classeModel->toutesLesClasses();
            $this->render('eleves/formulaire', [
                'title'      => 'Ajouter un élève',
                'pageTitle'  => 'Ajouter un élève',
                'user'       => AuthMiddleware::user(),
                'flash'      => null,
                'csrf_token' => $this->generateCsrfToken(),
                'classes'    => $classes,
                'eleve'      => $data,
                'errors'     => $errors,
            ]);
            return;
        }

        $data['matricule'] = $this->eleveModel->genererMatricule();
        $id = $this->eleveModel->insert($data);

        $this->flash('success', 'Élève ajouté avec succès. Matricule : ' . $data['matricule']);
        Router::redirect('eleves/fiche/' . $id);
    }

    // ─────────────────────────────────────────
    // GET /eleves/modifier/{id}
    // ─────────────────────────────────────────
    public function modifier(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);

        $id    = (int) $param;
        $eleve = $this->eleveModel->findById($id);

        if (!$eleve) {
            $this->flash('error', 'Élève introuvable.');
            Router::redirect('eleves');
        }

        $classes = $this->classeModel->toutesLesClasses();

        $this->render('eleves/formulaire', [
            'title'      => 'Modifier ' . $eleve['prenom'] . ' ' . $eleve['nom'],
            'pageTitle'  => 'Modifier un élève',
            'user'       => AuthMiddleware::user(),
            'flash'      => null,
            'csrf_token' => $this->generateCsrfToken(),
            'classes'    => $classes,
            'eleve'      => $eleve,
            'errors'     => [],
        ]);
    }

    // ─────────────────────────────────────────
    // POST /eleves/update/{id}
    // ─────────────────────────────────────────
    public function update(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);
        $this->requireMethod('POST');
        $this->validateCsrf();

        $id    = (int) $param;
        $eleve = $this->eleveModel->findById($id);

        if (!$eleve) {
            $this->flash('error', 'Élève introuvable.');
            Router::redirect('eleves');
        }

        $data   = $this->collectFormData();
        $errors = $this->valider($data, $id);

        if (!empty($errors)) {
            $classes = $this->classeModel->toutesLesClasses();
            $data['id']        = $id;
            $data['matricule'] = $eleve['matricule'];
            $this->render('eleves/formulaire', [
                'title'      => 'Modifier un élève',
                'pageTitle'  => 'Modifier un élève',
                'user'       => AuthMiddleware::user(),
                'flash'      => null,
                'csrf_token' => $this->generateCsrfToken(),
                'classes'    => $classes,
                'eleve'      => $data,
                'errors'     => $errors,
            ]);
            return;
        }

        $this->eleveModel->update($id, $data);
        $this->flash('success', 'Élève mis à jour avec succès.');
        Router::redirect('eleves/fiche/' . $id);
    }

    // ─────────────────────────────────────────
    // POST /eleves/supprimer/{id}
    // ─────────────────────────────────────────
    public function supprimer(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);
        $this->requireMethod('POST');
        $this->validateCsrf();

        $id = (int) $param;
        $this->eleveModel->desactiver($id);

        $this->flash('success', 'Élève supprimé avec succès.');
        Router::redirect('eleves');
    }

    // ─────────────────────────────────────────
    // HELPERS PRIVÉS
    // ─────────────────────────────────────────

    private function collectFormData(): array {
        return [
            'nom'            => strtoupper(trim($this->post('nom', ''))),
            'prenom'         => ucwords(strtolower(trim($this->post('prenom', '')))),
            'date_naissance' => $this->post('date_naissance', ''),
            'sexe'           => $this->post('sexe', ''),
            'classe_id'      => (int) $this->post('classe_id', 0),
            'parent_id'      => ($this->post('parent_id') ?: null),
            'actif'          => 1,
        ];
    }

    private function valider(array $data, int $excludeId = 0): array {
        $errors = [];

        if (empty($data['nom']))
            $errors['nom'] = 'Le nom est obligatoire.';

        if (empty($data['prenom']))
            $errors['prenom'] = 'Le prénom est obligatoire.';

        if (empty($data['date_naissance']))
            $errors['date_naissance'] = 'La date de naissance est obligatoire.';
        elseif (strtotime($data['date_naissance']) > strtotime('-3 years'))
            $errors['date_naissance'] = 'Date de naissance invalide.';

        if (!in_array($data['sexe'], ['M', 'F']))
            $errors['sexe'] = 'Le sexe est obligatoire.';

        if ($data['classe_id'] <= 0)
            $errors['classe_id'] = 'Veuillez choisir une classe.';

        return $errors;
    }
}
