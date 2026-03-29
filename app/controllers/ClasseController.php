<?php
// app/controllers/ClasseController.php

require_once ROOT_PATH . '/app/core/Controller.php';
require_once ROOT_PATH . '/app/models/Classe.php';
require_once ROOT_PATH . '/app/models/Matiere.php';
require_once ROOT_PATH . '/app/models/Eleve.php';

class ClasseController extends Controller {

    private Classe  $classeModel;
    private Matiere $matiereModel;

    public function __construct() {
        $this->classeModel  = new Classe();
        $this->matiereModel = new Matiere();
    }

    // ─────────────────────────────────────────
    // GET /classes — Liste des classes
    // ─────────────────────────────────────────
    public function index(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF]);

        $classes    = $this->classeModel->classesActives();
        $anneeActive = $this->classeModel->anneeActive();

        $this->render('classes/liste', [
            'title'      => 'Classes',
            'pageTitle'  => 'Gestion des classes',
            'user'       => AuthMiddleware::user(),
            'flash'      => $this->getFlash(),
            'classes'    => $classes,
            'annee'      => $anneeActive,
            'csrf_token' => $this->generateCsrfToken(),
        ]);
    }

    // ─────────────────────────────────────────
    // GET /classes/detail/{id}
    // ─────────────────────────────────────────
    public function detail(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF]);

        $id     = (int) $param;
        $classe = $this->classeModel->avecDetails($id);

        if (!$classe) {
            $this->flash('error', 'Classe introuvable.');
            Router::redirect('classes');
        }

        $matieres = $this->matiereModel->parClasse($id);
        $eleveModel = new Eleve();
        $eleves   = $eleveModel->parClasse($id);

        $this->render('classes/detail', [
            'title'      => $classe['nom'],
            'pageTitle'  => 'Détail de la classe',
            'user'       => AuthMiddleware::user(),
            'flash'      => $this->getFlash(),
            'classe'     => $classe,
            'matieres'   => $matieres,
            'eleves'     => $eleves,
            'csrf_token' => $this->generateCsrfToken(),
        ]);
    }

    // ─────────────────────────────────────────
    // GET /classes/ajouter
    // ─────────────────────────────────────────
    public function ajouter(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);

        $annees = $this->classeModel->toutesLesAnnees();

        $this->render('classes/formulaire', [
            'title'      => 'Ajouter une classe',
            'pageTitle'  => 'Ajouter une classe',
            'user'       => AuthMiddleware::user(),
            'csrf_token' => $this->generateCsrfToken(),
            'annees'     => $annees,
            'classe'     => null,
            'errors'     => [],
        ]);
    }

    // ─────────────────────────────────────────
    // POST /classes/store
    // ─────────────────────────────────────────
    public function store(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);
        $this->requireMethod('POST');
        $this->validateCsrf();

        $data   = $this->collectFormData();
        $errors = $this->valider($data);

        if (!empty($errors)) {
            $annees = $this->classeModel->toutesLesAnnees();
            $this->render('classes/formulaire', [
                'title'      => 'Ajouter une classe',
                'pageTitle'  => 'Ajouter une classe',
                'user'       => AuthMiddleware::user(),
                'csrf_token' => $this->generateCsrfToken(),
                'annees'     => $annees,
                'classe'     => $data,
                'errors'     => $errors,
            ]);
            return;
        }

        $this->classeModel->insert($data);
        $this->flash('success', 'Classe "' . $data['nom'] . '" créée avec succès.');
        Router::redirect('classes');
    }

    // ─────────────────────────────────────────
    // GET /classes/modifier/{id}
    // ─────────────────────────────────────────
    public function modifier(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);

        $id     = (int) $param;
        $classe = $this->classeModel->findById($id);

        if (!$classe) {
            $this->flash('error', 'Classe introuvable.');
            Router::redirect('classes');
        }

        $annees = $this->classeModel->toutesLesAnnees();

        $this->render('classes/formulaire', [
            'title'      => 'Modifier ' . $classe['nom'],
            'pageTitle'  => 'Modifier une classe',
            'user'       => AuthMiddleware::user(),
            'csrf_token' => $this->generateCsrfToken(),
            'annees'     => $annees,
            'classe'     => $classe,
            'errors'     => [],
        ]);
    }

    // ─────────────────────────────────────────
    // POST /classes/update/{id}
    // ─────────────────────────────────────────
    public function update(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);
        $this->requireMethod('POST');
        $this->validateCsrf();

        $id     = (int) $param;
        $classe = $this->classeModel->findById($id);

        if (!$classe) {
            $this->flash('error', 'Classe introuvable.');
            Router::redirect('classes');
        }

        $data   = $this->collectFormData();
        $errors = $this->valider($data);

        if (!empty($errors)) {
            $annees = $this->classeModel->toutesLesAnnees();
            $data['id'] = $id;
            $this->render('classes/formulaire', [
                'title'      => 'Modifier une classe',
                'pageTitle'  => 'Modifier une classe',
                'user'       => AuthMiddleware::user(),
                'csrf_token' => $this->generateCsrfToken(),
                'annees'     => $annees,
                'classe'     => $data,
                'errors'     => $errors,
            ]);
            return;
        }

        $this->classeModel->update($id, $data);
        $this->flash('success', 'Classe mise à jour avec succès.');
        Router::redirect('classes/detail/' . $id);
    }

    // ─────────────────────────────────────────
    // POST /classes/supprimer/{id}
    // ─────────────────────────────────────────
    public function supprimer(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);
        $this->requireMethod('POST');
        $this->validateCsrf();

        $id = (int) $param;
        $this->classeModel->delete($id);

        $this->flash('success', 'Classe supprimée.');
        Router::redirect('classes');
    }

    // ─────────────────────────────────────────
    // MATIÈRES — GET /classes/ajouterMatiere/{classe_id}
    // ─────────────────────────────────────────
    public function ajouterMatiere(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);

        $classeId = (int) $param;
        $classe   = $this->classeModel->findById($classeId);

        if (!$classe) {
            $this->flash('error', 'Classe introuvable.');
            Router::redirect('classes');
        }

        $profs = $this->classeModel->tousLesProfesseurs();

        $this->render('classes/formulaire_matiere', [
            'title'      => 'Ajouter une matière',
            'pageTitle'  => 'Ajouter une matière — ' . $classe['nom'],
            'user'       => AuthMiddleware::user(),
            'csrf_token' => $this->generateCsrfToken(),
            'classe'     => $classe,
            'matiere'    => null,
            'profs'      => $profs,
            'errors'     => [],
        ]);
    }

    // ─────────────────────────────────────────
    // POST /classes/storeMatiere
    // ─────────────────────────────────────────
    public function storeMatiere(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);
        $this->requireMethod('POST');
        $this->validateCsrf();

        $classeId    = (int) $this->post('classe_id', 0);
        $classe      = $this->classeModel->findById($classeId);

        if (!$classe) {
            $this->flash('error', 'Classe introuvable.');
            Router::redirect('classes');
        }

        $data = [
            'nom'         => trim($this->post('nom', '')),
            'coefficient' => (float) str_replace(',', '.', $this->post('coefficient', '1')),
            'classe_id'   => $classeId,
            'prof_id'     => $this->post('prof_id') ?: null,
        ];

        $errors = [];
        if (empty($data['nom']))
            $errors['nom'] = 'Le nom de la matière est obligatoire.';
        if ($data['coefficient'] <= 0 || $data['coefficient'] > 20)
            $errors['coefficient'] = 'Le coefficient doit être entre 0.5 et 20.';

        if (!empty($errors)) {
            $profs = $this->classeModel->tousLesProfesseurs();
            $this->render('classes/formulaire_matiere', [
                'title'      => 'Ajouter une matière',
                'pageTitle'  => 'Ajouter une matière — ' . $classe['nom'],
                'user'       => AuthMiddleware::user(),
                'csrf_token' => $this->generateCsrfToken(),
                'classe'     => $classe,
                'matiere'    => $data,
                'profs'      => $profs,
                'errors'     => $errors,
            ]);
            return;
        }

        $this->matiereModel->insert($data);
        $this->flash('success', 'Matière "' . $data['nom'] . '" ajoutée.');
        Router::redirect('classes/detail/' . $classeId);
    }

    // ─────────────────────────────────────────
    // POST /classes/supprimerMatiere/{id}
    // ─────────────────────────────────────────
    public function supprimerMatiere(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);
        $this->requireMethod('POST');
        $this->validateCsrf();

        $id      = (int) $param;
        $matiere = $this->matiereModel->findById($id);
        $classeId = $matiere['classe_id'] ?? 0;

        $this->matiereModel->delete($id);
        $this->flash('success', 'Matière supprimée.');
        Router::redirect('classes/detail/' . $classeId);
    }

    // ─────────────────────────────────────────
    // HELPERS PRIVÉS
    // ─────────────────────────────────────────
    private function collectFormData(): array {
        return [
            'nom'               => trim($this->post('nom', '')),
            'niveau'            => trim($this->post('niveau', '')),
            'annee_scolaire_id' => (int) $this->post('annee_scolaire_id', 0),
        ];
    }

    private function valider(array $data): array {
        $errors = [];
        if (empty($data['nom']))
            $errors['nom'] = 'Le nom de la classe est obligatoire.';
        if (empty($data['niveau']))
            $errors['niveau'] = 'Le niveau est obligatoire.';
        if ($data['annee_scolaire_id'] <= 0)
            $errors['annee_scolaire_id'] = 'Veuillez choisir une année scolaire.';
        return $errors;
    }
}
