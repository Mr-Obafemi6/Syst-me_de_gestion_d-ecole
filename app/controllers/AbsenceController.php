<?php
// app/controllers/AbsenceController.php

require_once ROOT_PATH . '/app/core/Controller.php';
require_once ROOT_PATH . '/app/models/Absence.php';
require_once ROOT_PATH . '/app/models/Eleve.php';
require_once ROOT_PATH . '/app/models/Classe.php';

class AbsenceController extends Controller {

    private Absence $absenceModel;
    private Eleve   $eleveModel;
    private Classe  $classeModel;

    public function __construct() {
        $this->absenceModel = new Absence();
        $this->eleveModel   = new Eleve();
        $this->classeModel  = new Classe();
    }

    // ─────────────────────────────────────────
    // GET /absences — Liste paginée
    // ─────────────────────────────────────────
    public function index(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF]);

        $page      = max(1, (int) $this->get('page', 1));
        $classeId  = (int) $this->get('classe', 0);
        $eleveId   = (int) $this->get('eleve', 0);
        $justifiee = $this->get('justifiee', '');

        $pagination      = $this->absenceModel->listerAvecDetails($page, $classeId, $eleveId, $justifiee);
        $classes         = $this->classeModel->toutesLesClasses();
        $absencesAujourd = $this->absenceModel->absencesAujourdhui();

        $this->render('absences/liste', [
            'title'           => 'Absences',
            'pageTitle'       => 'Gestion des absences',
            'user'            => AuthMiddleware::user(),
            'flash'           => $this->getFlash(),
            'csrf_token'      => $this->generateCsrfToken(),
            'pagination'      => $pagination,
            'classes'         => $classes,
            'classeId'        => $classeId,
            'eleveId'         => $eleveId,
            'justifiee'       => $justifiee,
            'absencesAujourd' => $absencesAujourd,
        ]);
    }

    // ─────────────────────────────────────────
    // GET /absences/ajouter?eleve=X
    // ─────────────────────────────────────────
    public function ajouter(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF]);

        $eleveId = (int) $this->get('eleve', 0);
        $eleve   = $eleveId ? $this->eleveModel->ficheComplete($eleveId) : null;
        $classes = $this->classeModel->toutesLesClasses();

        $this->render('absences/formulaire', [
            'title'      => 'Ajouter une absence',
            'pageTitle'  => 'Enregistrer une absence',
            'user'       => AuthMiddleware::user(),
            'csrf_token' => $this->generateCsrfToken(),
            'eleve'      => $eleve,
            'classes'    => $classes,
            'errors'     => [],
            'data'       => [],
        ]);
    }

    // ─────────────────────────────────────────
    // POST /absences/store
    // ─────────────────────────────────────────
    public function store(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF]);
        $this->requireMethod('POST');
        $this->validateCsrf();

        $user    = AuthMiddleware::user();
        $eleveId = (int) $this->post('eleve_id', 0);
        $eleve   = $eleveId ? $this->eleveModel->ficheComplete($eleveId) : null;

        $data = [
            'eleve_id'     => $eleveId,
            'date_absence' => $this->post('date_absence', date('Y-m-d')),
            'heure_debut'  => $this->post('heure_debut', '') ?: null,
            'heure_fin'    => $this->post('heure_fin', '')   ?: null,
            'motif'        => $this->post('motif', 'non_justifie'),
            'justifiee'    => (int) $this->post('justifiee', 0),
            'commentaire'  => trim($this->post('commentaire', '')) ?: null,
            'saisie_par'   => $user['id'],
        ];

        $errors = $this->valider($data, $eleve);

        if (!empty($errors)) {
            $classes = $this->classeModel->toutesLesClasses();
            $this->render('absences/formulaire', [
                'title'      => 'Ajouter une absence',
                'pageTitle'  => 'Enregistrer une absence',
                'user'       => $user,
                'csrf_token' => $this->generateCsrfToken(),
                'eleve'      => $eleve,
                'classes'    => $classes,
                'errors'     => $errors,
                'data'       => $data,
            ]);
            return;
        }

        $this->absenceModel->insert($data);
        $this->flash('success', 'Absence enregistrée pour ' . ($eleve['prenom'] ?? '') . ' ' . ($eleve['nom'] ?? '') . '.');
        Router::redirect('absences?eleve=' . $eleveId);
    }

    // ─────────────────────────────────────────
    // POST /absences/justifier/{id}
    // ─────────────────────────────────────────
    public function justifier(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF]);
        $this->requireMethod('POST');
        $this->validateCsrf();

        $id = (int) $param;
        $this->absenceModel->update($id, ['justifiee' => 1]);

        $this->flash('success', 'Absence marquée comme justifiée.');
        Router::redirect('absences');
    }

    // ─────────────────────────────────────────
    // POST /absences/supprimer/{id}
    // ─────────────────────────────────────────
    public function supprimer(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF]);
        $this->requireMethod('POST');
        $this->validateCsrf();

        $id      = (int) $param;
        $absence = $this->absenceModel->findById($id);
        $eleveId = $absence['eleve_id'] ?? 0;

        $this->absenceModel->delete($id);
        $this->flash('success', 'Absence supprimée.');
        Router::redirect('absences' . ($eleveId ? '?eleve=' . $eleveId : ''));
    }

    // ─────────────────────────────────────────
    // GET /absences/eleve/{id}
    // ─────────────────────────────────────────
    public function eleve(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF, ROLE_PARENT]);

        $id    = (int) $param;
        $eleve = $this->eleveModel->ficheComplete($id);

        if (!$eleve) {
            $this->flash('error', 'Élève introuvable.');
            Router::redirect('absences');
        }

        // Parent : uniquement son enfant
        if (AuthMiddleware::hasRole(ROLE_PARENT)) {
            $me = AuthMiddleware::user();
            if ($eleve['parent_id'] != $me['id']) {
                Router::redirect('dashboard');
            }
        }

        $absences = $this->absenceModel->parEleve($id);
        $stats    = $this->absenceModel->statsParEleve($id);

        $this->render('absences/eleve', [
            'title'      => 'Absences — ' . $eleve['prenom'] . ' ' . $eleve['nom'],
            'pageTitle'  => 'Absences de l\'élève',
            'user'       => AuthMiddleware::user(),
            'flash'      => $this->getFlash(),
            'csrf_token' => $this->generateCsrfToken(),
            'eleve'      => $eleve,
            'absences'   => $absences,
            'stats'      => $stats,
        ]);
    }

    // ─────────────────────────────────────────
    // GET /absences/classe/{id}
    // ─────────────────────────────────────────
    public function classe(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF]);

        $id     = (int) $param;
        $classe = $this->classeModel->avecDetails($id);

        if (!$classe) {
            $this->flash('error', 'Classe introuvable.');
            Router::redirect('absences');
        }

        $stats = $this->absenceModel->statsParClasse($id);

        $this->render('absences/classe', [
            'title'      => 'Absences — ' . $classe['nom'],
            'pageTitle'  => 'Absences de la classe ' . $classe['nom'],
            'user'       => AuthMiddleware::user(),
            'flash'      => $this->getFlash(),
            'classe'     => $classe,
            'stats'      => $stats,
        ]);
    }

    // ─────────────────────────────────────────
    // HELPERS PRIVÉS
    // ─────────────────────────────────────────
    private function valider(array $data, ?array $eleve): array {
        $errors = [];
        if (!$eleve)
            $errors['eleve_id'] = 'Veuillez sélectionner un élève valide.';
        if (empty($data['date_absence']))
            $errors['date_absence'] = 'La date est obligatoire.';
        if (!in_array($data['motif'], ['maladie', 'familial', 'non_justifie', 'autre']))
            $errors['motif'] = 'Motif invalide.';
        return $errors;
    }
}
