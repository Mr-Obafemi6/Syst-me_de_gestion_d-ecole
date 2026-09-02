<?php
require_once ROOT_PATH . '/app/core/Controller.php';
require_once ROOT_PATH . '/app/models/Matiere.php';
require_once ROOT_PATH . '/app/models/Classe.php';

class MatiereController extends Controller {
    private Matiere $matiereModel;
    private Classe $classeModel;

    public function __construct() {
        $this->matiereModel = new Matiere();
        $this->classeModel = new Classe();
    }

    public function index(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF]);

        $classesAvecMatieres = $this->matiereModel->classesAvecMatieres();
        $catalogue = $this->matiereModel->catalogue();

        $groupes = [
            'college' => ['label' => 'Collège — 6ème à 3ème', 'classes' => []],
            'lycee'   => ['label' => 'Lycée — 2nde à Terminale', 'classes' => []],
        ];
        foreach ($classesAvecMatieres as $classe) {
            $cycle = $classe['niveau_cycle'] ?? 'college';
            $groupes[$cycle]['classes'][] = $classe;
        }
        $groupes = array_filter($groupes, fn($g) => !empty($g['classes']));

        $this->render('matieres/index', [
            'title' => 'Matières',
            'pageTitle' => 'Gestion des matières par classe',
            'user' => AuthMiddleware::user(),
            'flash' => $this->getFlash(),
            'csrf_token' => $this->generateCsrfToken(),
            'groupes' => $groupes,
            'catalogue' => $catalogue,
        ]);
    }

    public function ajouter(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);

        $this->render('matieres/formulaire', [
            'title' => 'Ajouter une matière',
            'pageTitle' => 'Ajouter une matière au catalogue',
            'user' => AuthMiddleware::user(),
            'csrf_token' => $this->generateCsrfToken(),
            'matiere' => null,
            'classes' => $this->classeModel->classesActives(),
            'profs' => $this->classeModel->tousLesProfesseurs(),
            'errors' => [],
        ]);
    }

    public function store(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);
        $this->requireMethod('POST');
        $this->validateCsrf();

        $data = $this->collectFormData();
        $errors = $this->valider($data);

        if (!empty($errors)) {
            $this->render('matieres/formulaire', [
                'title' => 'Ajouter une matière',
                'pageTitle' => 'Ajouter une matière au catalogue',
                'user' => AuthMiddleware::user(),
                'csrf_token' => $this->generateCsrfToken(),
                'matiere' => $data,
                'classes' => $this->classeModel->classesActives(),
                'profs' => $this->classeModel->tousLesProfesseurs(),
                'errors' => $errors,
            ]);
            return;
        }

        $matiereId = $this->matiereModel->insert([
            'nom' => $data['nom'],
            'code' => $data['code'],
            'description' => $data['description'],
            'coefficient' => $data['coefficient'],
            'statut' => $data['statut'],
            'prof_id' => $data['prof_id'] ?: null,
        ]);

        if (!empty($data['classes'])) {
            foreach ($data['classes'] as $classeId) {
                $this->matiereModel->assignerAClasse($matiereId, (int) $classeId, $data['coefficient']);
            }
        }

        $this->flash('success', 'Matière créée avec succès.');
        Router::redirect('matieres');
    }

    public function modifier(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);

        $id = (int) $param;
        $matiere = $this->matiereModel->findById($id);
        if (!$matiere) {
            $this->flash('error', 'Matière introuvable.');
            Router::redirect('matieres');
        }

        $this->render('matieres/formulaire', [
            'title' => 'Modifier une matière',
            'pageTitle' => 'Modifier une matière',
            'user' => AuthMiddleware::user(),
            'csrf_token' => $this->generateCsrfToken(),
            'matiere' => $matiere,
            'classes' => $this->classeModel->classesActives(),
            'profs' => $this->classeModel->tousLesProfesseurs(),
            'errors' => [],
        ]);
    }

    public function update(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);
        $this->requireMethod('POST');
        $this->validateCsrf();

        $id = (int) $param;
        if (!$this->matiereModel->findById($id)) {
            $this->flash('error', 'Matière introuvable.');
            Router::redirect('matieres');
        }

        $data = $this->collectFormData();
        $errors = $this->valider($data);

        if (!empty($errors)) {
            $this->render('matieres/formulaire', [
                'title' => 'Modifier une matière',
                'pageTitle' => 'Modifier une matière',
                'user' => AuthMiddleware::user(),
                'csrf_token' => $this->generateCsrfToken(),
                'matiere' => array_merge($data, ['id' => $id]),
                'classes' => $this->classeModel->classesActives(),
                'profs' => $this->classeModel->tousLesProfesseurs(),
                'errors' => $errors,
            ]);
            return;
        }

        $this->matiereModel->update($id, [
            'nom' => $data['nom'],
            'code' => $data['code'],
            'description' => $data['description'],
            'coefficient' => $data['coefficient'],
            'statut' => $data['statut'],
            'prof_id' => $data['prof_id'] ?: null,
        ]);

        $db = Database::getConnection();
        $db->prepare("DELETE FROM `classe_matieres` WHERE `matiere_id` = ?")->execute([$id]);
        if (!empty($data['classes'])) {
            foreach ($data['classes'] as $classeId) {
                $this->matiereModel->assignerAClasse($id, (int) $classeId, $data['coefficient']);
            }
        }

        $this->flash('success', 'Matière mise à jour avec succès.');
        Router::redirect('matieres');
    }

    public function supprimer(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);
        $this->requireMethod('POST');
        $this->validateCsrf();

        $id = (int) $param;
        Database::getConnection()->prepare("DELETE FROM `classe_matieres` WHERE `matiere_id` = ?")->execute([$id]);
        $this->matiereModel->delete($id);

        $this->flash('success', 'Matière supprimée.');
        Router::redirect('matieres');
    }

    /**
     * POST /matieres/assigner — Assigner une matière du catalogue à une classe
     */
    public function assigner(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);
        $this->requireMethod('POST');
        $this->validateCsrf();

        $classeId   = (int) $this->post('classe_id', 0);
        $matiereId  = (int) $this->post('matiere_id', 0);
        $coefficient = (float) str_replace(',', '.', (string) $this->post('coefficient', '1'));

        if ($classeId <= 0 || $matiereId <= 0) {
            $this->flash('error', 'Classe ou matière invalide.');
            Router::redirect('matieres');
        }

        if ($coefficient <= 0 || $coefficient > 20) {
            $this->flash('error', 'Coefficient invalide (0,5 à 20).');
            Router::redirect('matieres');
        }

        $this->matiereModel->assignerAClasse($matiereId, $classeId, $coefficient);
        $this->flash('success', 'Matière assignée à la classe.');
        Router::redirect('matieres');
    }

    /**
     * POST /matieres/retirer — Retirer une matière d'une classe
     */
    public function retirer(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);
        $this->requireMethod('POST');
        $this->validateCsrf();

        $classeId  = (int) $this->post('classe_id', 0);
        $matiereId = (int) $this->post('matiere_id', 0);

        if ($classeId <= 0 || $matiereId <= 0) {
            $this->flash('error', 'Classe ou matière invalide.');
            Router::redirect('matieres');
        }

        $this->matiereModel->retirerDeClasse($matiereId, $classeId);
        $this->flash('success', 'Matière retirée de la classe.');
        Router::redirect('matieres');
    }

    private function collectFormData(): array {
        return [
            'nom' => trim((string) $this->post('nom', '')),
            'code' => trim((string) $this->post('code', '')),
            'description' => trim((string) $this->post('description', '')),
            'coefficient' => (float) str_replace(',', '.', (string) $this->post('coefficient', '1')),
            'statut' => (int) $this->post('statut', 1),
            'prof_id' => $this->post('prof_id') !== '' ? (int) $this->post('prof_id', 0) : null,
            'classes' => isset($_POST['classes']) ? array_map('intval', (array) $_POST['classes']) : [],
        ];
    }

    private function valider(array $data): array {
        $errors = [];
        if (empty($data['nom'])) {
            $errors['nom'] = 'Le nom de la matière est obligatoire.';
        }
        if ($data['coefficient'] <= 0 || $data['coefficient'] > 20) {
            $errors['coefficient'] = 'Le coefficient doit être compris entre 0,5 et 20.';
        }
        return $errors;
    }
}
