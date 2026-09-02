<?php
// app/controllers/EvenementController.php

require_once ROOT_PATH . '/app/core/Controller.php';
require_once ROOT_PATH . '/app/models/Evenement.php';
require_once ROOT_PATH . '/app/models/Classe.php';

class EvenementController extends Controller {

    private Evenement $evenementModel;
    private Classe    $classeModel;

    public function __construct() {
        $this->evenementModel = new Evenement();
        $this->classeModel    = new Classe();
    }

    // ─────────────────────────────────────────
    // GET /evenements — Liste
    // ─────────────────────────────────────────
    public function index(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF]);

        $page       = max(1, (int) $this->get('page', 1));
        $pagination = $this->evenementModel->listerAvecDetails($page);

        $this->render('evenements/liste', [
            'title'      => 'Événements',
            'pageTitle'  => 'Agenda — Événements',
            'user'       => AuthMiddleware::user(),
            'flash'      => $this->getFlash(),
            'csrf_token' => $this->generateCsrfToken(),
            'pagination' => $pagination,
        ]);
    }

    // ─────────────────────────────────────────
    // GET /evenements/ajouter
    // ─────────────────────────────────────────
    public function ajouter(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF]);

        $classes = $this->classeModel->toutesLesClasses();

        $this->render('evenements/formulaire', [
            'title'      => 'Ajouter un événement',
            'pageTitle'  => 'Nouvel événement',
            'user'       => AuthMiddleware::user(),
            'csrf_token' => $this->generateCsrfToken(),
            'classes'    => $classes,
            'errors'     => [],
            'data'       => [],
        ]);
    }

    // ─────────────────────────────────────────
    // POST /evenements/store
    // ─────────────────────────────────────────
    public function store(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF]);
        $this->requireMethod('POST');
        $this->validateCsrf();

        $user = AuthMiddleware::user();

        $data = [
            'titre'       => trim($this->post('titre', '')),
            'description' => trim($this->post('description', '')) ?: null,
            'date_debut'  => $this->post('date_debut', ''),
            'type'        => $this->post('type', 'autre'),
            'classe_id'   => (int) $this->post('classe_id', 0) ?: null,
            'cree_par'    => $user['id'],
        ];

        $errors = $this->valider($data);

        if (!empty($errors)) {
            $classes = $this->classeModel->toutesLesClasses();
            $this->render('evenements/formulaire', [
                'title'      => 'Ajouter un événement',
                'pageTitle'  => 'Nouvel événement',
                'user'       => $user,
                'csrf_token' => $this->generateCsrfToken(),
                'classes'    => $classes,
                'errors'     => $errors,
                'data'       => $data,
            ]);
            return;
        }

        // Gestion de l'upload de photo
        $photoPath = $this->storeUploadedFile($_FILES['photo'] ?? [], 'evenements');
        if ($photoPath) {
            $data['photo'] = $photoPath;
        }

        $this->evenementModel->insert($data);

        $this->flash('success', 'Événement « ' . htmlspecialchars($data['titre']) . ' » ajouté à l\'agenda.');
        Router::redirect('evenements');
    }

    // ─────────────────────────────────────────
    // POST /evenements/supprimer/{id}
    // ─────────────────────────────────────────
    public function supprimer(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF]);
        $this->requireMethod('POST');
        $this->validateCsrf();

        $id = (int) $param;
        $this->evenementModel->delete($id);

        $this->flash('success', 'Événement supprimé.');
        Router::redirect('evenements');
    }

    // ─────────────────────────────────────────
    // HELPERS PRIVÉS
    // ─────────────────────────────────────────
    private function valider(array $data): array {
        $errors = [];
        if (empty($data['titre']))
            $errors['titre'] = 'Le titre est obligatoire.';
        if (empty($data['date_debut']))
            $errors['date_debut'] = 'La date et l\'heure sont obligatoires.';
        if (!in_array($data['type'], ['reunion','sortie','examen','conseil','autre']))
            $errors['type'] = 'Type invalide.';
        return $errors;
    }
}
