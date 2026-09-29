<?php

require_once ROOT_PATH . '/app/core/Controller.php';
require_once ROOT_PATH . '/app/models/Enseignant.php';
require_once ROOT_PATH . '/app/models/User.php';

class EnseignantController extends Controller {
    private Enseignant $enseignantModel;
    private User $userModel;

    public function __construct() {
        $this->enseignantModel = new Enseignant();
        $this->userModel = new User();
    }

    public function index(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);
        AuthorizationService::getInstance()->requirePermission('teachers.view');

        $q = trim((string) $this->get('q', ''));
        $statut = trim((string) $this->get('statut', ''));
        $teachers = $this->enseignantModel->list($q, $statut);

        $this->render('enseignants/liste', [
            'title' => 'Enseignants',
            'pageTitle' => 'Gestion des enseignants',
            'user' => AuthMiddleware::user(),
            'flash' => $this->getFlash(),
            'csrf_token' => $this->generateCsrfToken(),
            'teachers' => $teachers,
            'q' => $q,
            'statut' => $statut,
        ]);
    }

    public function ajouter(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);
        AuthorizationService::getInstance()->requirePermission('teachers.manage');

        $this->render('enseignants/formulaire', [
            'title' => 'Ajouter un enseignant',
            'pageTitle' => 'Nouvel enseignant',
            'user' => AuthMiddleware::user(),
            'flash' => null,
            'csrf_token' => $this->generateCsrfToken(),
            'teacher' => null,
            'errors' => [],
        ]);
    }

    public function fiche(?string $param = null): void {
        $id = (int) $param;
        $teacher = $this->enseignantModel->findById($id);

        if (!$teacher) {
            $this->flash('error', 'Enseignant introuvable.');
            Router::redirect('enseignants');
        }

        if (AuthMiddleware::hasRole(ROLE_PROF) && (int) $teacher['user_id'] !== (int) (AuthMiddleware::user()['id'] ?? 0)) {
            http_response_code(403);
            require ROOT_PATH . '/app/views/errors/403.php';
            exit;
        }

        if (AuthMiddleware::hasRole(ROLE_ADMIN)) {
            AuthorizationService::getInstance()->requirePermission('teachers.view');
        }

        $teacher['classes'] = $this->enseignantModel->classesForTeacher((int) $teacher['user_id']);
        $teacher['matieres'] = $this->enseignantModel->subjectsForTeacher((int) $teacher['user_id']);
        $teacher['assignments'] = $this->enseignantModel->assignmentsForTeacher((int) $teacher['user_id']);

        $this->render('enseignants/fiche', [
            'title' => 'Profil enseignant',
            'pageTitle' => 'Profil enseignant',
            'user' => AuthMiddleware::user(),
            'flash' => $this->getFlash(),
            'csrf_token' => $this->generateCsrfToken(),
            'teacher' => $teacher,
            'permissions' => $this->enseignantModel->permissionsForUser((int) $teacher['user_id']),
            'canEditPermissions' => AuthMiddleware::hasRole(ROLE_ADMIN),
        ]);
    }

    public function save(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);
        AuthorizationService::getInstance()->requirePermission('teachers.manage');
        $this->requireMethod('POST');
        $this->validateCsrf();

        $userId = (int) $this->post('user_id', 0);
        $nom = trim((string) $this->post('nom', ''));
        $prenom = trim((string) $this->post('prenom', ''));
        $email = trim((string) strtolower($this->post('email', '')));
        $telephone = trim((string) $this->post('telephone', ''));
        $matricule = trim((string) $this->post('matricule', ''));

        $errors = [];
        if ($nom === '') $errors['nom'] = 'Le nom est obligatoire.';
        if ($prenom === '') $errors['prenom'] = 'Le prénom est obligatoire.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'L’email est invalide.';
        if ($matricule === '') $errors['matricule'] = 'Le matricule est obligatoire.';

        if (!empty($errors)) {
            $this->render('enseignants/formulaire', [
                'title' => 'Ajouter un enseignant',
                'pageTitle' => 'Nouvel enseignant',
                'user' => AuthMiddleware::user(),
                'flash' => null,
                'csrf_token' => $this->generateCsrfToken(),
                'teacher' => [
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'email' => $email,
                    'telephone' => $telephone,
                    'matricule' => $matricule,
                ],
                'errors' => $errors,
            ]);
            return;
        }

        if ($userId > 0) {
            $user = $this->userModel->findById($userId);
            if (!$user) {
                $this->flash('error', 'Compte utilisateur introuvable.');
                Router::redirect('enseignants');
            }
            $this->userModel->update($userId, [
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'photo' => $this->post('photo', $user['photo'] ?? ''),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            if ($this->userModel->emailExists($email)) {
                $this->flash('error', 'Un compte avec cet email existe déjà.');
                Router::redirect('enseignants/ajouter');
            }
            $plain = $this->userModel->genererMotDePasseTemporaire();
            $userId = $this->userModel->createUser([
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'password' => $plain,
                'role' => ROLE_PROF,
                'actif' => 1,
            ]);
            $this->flash('success', 'Compte enseignant créé. Identifiant : ' . $email . ' / mot de passe temporaire : ' . $plain);
        }

        $teacherData = [
            'user_id' => $userId,
            'nom' => $nom,
            'prenom' => $prenom,
            'sexe' => $this->post('sexe', 'M'),
            'date_naissance' => $this->post('date_naissance', null),
            'lieu_naissance' => $this->post('lieu_naissance', ''),
            'nationalite' => $this->post('nationalite', ''),
            'telephone' => $telephone,
            'email' => $email,
            'adresse' => $this->post('adresse', ''),
            'photo' => $this->post('photo', ''),
            'matricule' => $matricule,
            'niveau_etude' => $this->post('niveau_etude', ''),
            'specialite' => $this->post('specialite', ''),
            'diplome' => $this->post('diplome', ''),
            'experience' => $this->post('experience', ''),
            'date_recrutement' => $this->post('date_recrutement', null),
            'statut_professionnel' => $this->post('statut_professionnel', ''),
            'type_contrat' => $this->post('type_contrat', ''),
            'telephone_professionnel' => $this->post('telephone_professionnel', ''),
            'email_professionnel' => $this->post('email_professionnel', ''),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->enseignantModel->upsertProfile($userId, $teacherData);

        $this->flash('success', 'Enseignant enregistré avec succès.');
        Router::redirect('enseignants/fiche/' . $userId);
    }

    public function permissions(?string $param = null): void {
        $userId = (int) ($param ?: $this->get('user_id', 0));
        if ($userId <= 0) {
            $this->flash('error', 'Aucun enseignant sélectionné.');
            Router::redirect('enseignants');
        }

        AuthMiddleware::requireRole(ROLE_ADMIN);
        AuthorizationService::getInstance()->requirePermission('teachers.manage');

        $teacher = $this->enseignantModel->byUserId($userId);
        if (!$teacher) {
            $this->flash('error', 'Enseignant introuvable.');
            Router::redirect('enseignants');
        }

        $this->render('enseignants/permissions', [
            'title' => 'Permissions enseignant',
            'pageTitle' => 'Permissions',
            'user' => AuthMiddleware::user(),
            'flash' => $this->getFlash(),
            'csrf_token' => $this->generateCsrfToken(),
            'teacher' => $teacher,
            'permissions' => $this->enseignantModel->permissionsForUser($userId),
        ]);
    }

    public function savePermissions(?string $param = null): void {
        $userId = (int) ($param ?: $this->post('user_id', 0));
        AuthMiddleware::requireRole(ROLE_ADMIN);
        AuthorizationService::getInstance()->requirePermission('teachers.manage');
        $this->requireMethod('POST');
        $this->validateCsrf();

        $permissions = [];
        foreach ((array) ($_POST['permission'] ?? []) as $permissionId => $effect) {
            if (in_array($effect, ['allow', 'deny'], true)) {
                $permissions[(int) $permissionId] = $effect;
            }
        }

        $this->enseignantModel->savePermissions($userId, $permissions);
        $this->flash('success', 'Permissions mises à jour.');
        Router::redirect('enseignants/fiche/' . $userId);
    }

    public function bloquer(?string $param = null): void {
        $userId = (int) $param;
        AuthMiddleware::requireRole(ROLE_ADMIN);
        AuthorizationService::getInstance()->requirePermission('teachers.manage');
        $this->requireMethod('POST');
        $this->validateCsrf();
        $this->userModel->update($userId, ['actif' => 0]);
        $this->flash('success', 'Compte bloqué. L’enseignant ne peut plus se connecter.');
        Router::redirect('enseignants');
    }

    public function activer(?string $param = null): void {
        $userId = (int) $param;
        AuthMiddleware::requireRole(ROLE_ADMIN);
        AuthorizationService::getInstance()->requirePermission('teachers.manage');
        $this->requireMethod('POST');
        $this->validateCsrf();
        $this->userModel->update($userId, ['actif' => 1]);
        $this->flash('success', 'Compte réactivé.');
        Router::redirect('enseignants');
    }

    public function resetPassword(?string $param = null): void {
        $userId = (int) $param;
        AuthMiddleware::requireRole(ROLE_ADMIN);
        AuthorizationService::getInstance()->requirePermission('teachers.manage');
        $this->requireMethod('POST');
        $this->validateCsrf();
        $newPassword = $this->userModel->genererMotDePasseTemporaire();
        $this->userModel->changePassword($userId, $newPassword);
        $this->flash('success', 'Mot de passe réinitialisé. Nouveau mot de passe temporaire : ' . $newPassword);
        Router::redirect('enseignants/fiche/' . $userId);
    }
}
