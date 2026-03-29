<?php
// app/controllers/ParametreController.php

require_once ROOT_PATH . '/app/core/Controller.php';
require_once ROOT_PATH . '/app/models/User.php';
require_once ROOT_PATH . '/app/models/Classe.php';

class ParametreController extends Controller {

    private User   $userModel;
    private Classe $classeModel;

    public function __construct() {
        $this->userModel   = new User();
        $this->classeModel = new Classe();
    }

    // ─────────────────────────────────────────
    // GET /parametres
    // ─────────────────────────────────────────
    public function index(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);

        $params  = $this->getAllParams();
        $users   = $this->userModel->getAllWithRole();
        $annees  = $this->classeModel->toutesLesAnnees();

        $this->render('parametres/index', [
            'title'      => 'Paramètres',
            'pageTitle'  => "Paramètres de l'école",
            'user'       => AuthMiddleware::user(),
            'flash'      => $this->getFlash(),
            'csrf_token' => $this->generateCsrfToken(),
            'params'     => $params,
            'users'      => $users,
            'annees'     => $annees,
        ]);
    }

    // ─────────────────────────────────────────
    // POST /parametres/saveEcole
    // ─────────────────────────────────────────
    public function saveEcole(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);
        $this->requireMethod('POST');
        $this->validateCsrf();

        $champs = [
            'nom_ecole', 'adresse', 'telephone', 'email',
            'frais_scol_primaire', 'frais_scol_college', 'frais_scol_lycee',
        ];

        $db = Database::getConnection();
        foreach ($champs as $cle) {
            $val = trim($this->post($cle, ''));
            $stmt = $db->prepare(
                "INSERT INTO `parametres` (cle, valeur)
                 VALUES (?, ?)
                 ON DUPLICATE KEY UPDATE valeur = VALUES(valeur)"
            );
            $stmt->execute([$cle, $val]);
        }

        $this->flash('success', 'Paramètres de l\'école enregistrés.');
        Router::redirect('parametres');
    }

    // ─────────────────────────────────────────
    // POST /parametres/saveAnnee
    // ─────────────────────────────────────────
    public function saveAnnee(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);
        $this->requireMethod('POST');
        $this->validateCsrf();

        $libelle    = trim($this->post('libelle', ''));
        $dateDebut  = $this->post('date_debut', '');
        $dateFin    = $this->post('date_fin', '');
        $setActive  = (int) $this->post('active', 0);

        if (empty($libelle) || empty($dateDebut) || empty($dateFin)) {
            $this->flash('error', 'Tous les champs de l\'année scolaire sont obligatoires.');
            Router::redirect('parametres');
        }

        $db = Database::getConnection();

        // Si on active cette année, désactiver les autres
        if ($setActive) {
            $db->exec("UPDATE `annees_scolaires` SET active = 0");
        }

        // Insérer ou mettre à jour
        $existing = $db->prepare("SELECT id FROM `annees_scolaires` WHERE libelle = ?");
        $existing->execute([$libelle]);
        $row = $existing->fetch();

        if ($row) {
            $stmt = $db->prepare(
                "UPDATE `annees_scolaires`
                 SET date_debut = ?, date_fin = ?, active = ?
                 WHERE id = ?"
            );
            $stmt->execute([$dateDebut, $dateFin, $setActive, $row['id']]);
        } else {
            $stmt = $db->prepare(
                "INSERT INTO `annees_scolaires` (libelle, date_debut, date_fin, active)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$libelle, $dateDebut, $dateFin, $setActive]);
        }

        $this->flash('success', 'Année scolaire enregistrée.');
        Router::redirect('parametres');
    }

    // ─────────────────────────────────────────
    // POST /parametres/activerAnnee/{id}
    // ─────────────────────────────────────────
    public function activerAnnee(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);
        $this->requireMethod('POST');
        $this->validateCsrf();

        $id = (int) $param;
        $db = Database::getConnection();
        $db->exec("UPDATE `annees_scolaires` SET active = 0");
        $db->prepare("UPDATE `annees_scolaires` SET active = 1 WHERE id = ?")->execute([$id]);

        $this->flash('success', 'Année scolaire activée.');
        Router::redirect('parametres');
    }

    // ─────────────────────────────────────────
    // GET /parametres/ajouterUser
    // ─────────────────────────────────────────
    public function ajouterUser(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);

        $this->render('parametres/formulaire_user', [
            'title'      => 'Ajouter un utilisateur',
            'pageTitle'  => 'Ajouter un utilisateur',
            'user'       => AuthMiddleware::user(),
            'csrf_token' => $this->generateCsrfToken(),
            'userEdit'   => null,
            'errors'     => [],
        ]);
    }

    // ─────────────────────────────────────────
    // POST /parametres/storeUser
    // ─────────────────────────────────────────
    public function storeUser(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);
        $this->requireMethod('POST');
        $this->validateCsrf();

        $data   = $this->collectUserData();
        $errors = $this->validerUser($data);

        if ($this->userModel->emailExists($data['email'])) {
            $errors['email'] = 'Cet email est déjà utilisé.';
        }

        if (empty($data['password'])) {
            $errors['password'] = 'Le mot de passe est obligatoire.';
        } elseif (strlen($data['password']) < 8) {
            $errors['password'] = 'Le mot de passe doit contenir au moins 8 caractères.';
        }

        if (!empty($errors)) {
            $this->render('parametres/formulaire_user', [
                'title'      => 'Ajouter un utilisateur',
                'pageTitle'  => 'Ajouter un utilisateur',
                'user'       => AuthMiddleware::user(),
                'csrf_token' => $this->generateCsrfToken(),
                'userEdit'   => $data,
                'errors'     => $errors,
            ]);
            return;
        }

        $this->userModel->createUser($data);
        $this->flash('success', 'Utilisateur créé : ' . $data['email']);
        Router::redirect('parametres');
    }

    // ─────────────────────────────────────────
    // GET /parametres/modifierUser/{id}
    // ─────────────────────────────────────────
    public function modifierUser(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);

        $id      = (int) $param;
        $userEdit = $this->userModel->findById($id);

        if (!$userEdit) {
            $this->flash('error', 'Utilisateur introuvable.');
            Router::redirect('parametres');
        }

        $this->render('parametres/formulaire_user', [
            'title'      => 'Modifier ' . $userEdit['prenom'] . ' ' . $userEdit['nom'],
            'pageTitle'  => 'Modifier un utilisateur',
            'user'       => AuthMiddleware::user(),
            'csrf_token' => $this->generateCsrfToken(),
            'userEdit'   => $userEdit,
            'errors'     => [],
        ]);
    }

    // ─────────────────────────────────────────
    // POST /parametres/updateUser/{id}
    // ─────────────────────────────────────────
    public function updateUser(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);
        $this->requireMethod('POST');
        $this->validateCsrf();

        $id      = (int) $param;
        $userEdit = $this->userModel->findById($id);

        if (!$userEdit) {
            $this->flash('error', 'Utilisateur introuvable.');
            Router::redirect('parametres');
        }

        $data   = $this->collectUserData();
        $errors = $this->validerUser($data);

        if ($this->userModel->emailExists($data['email'], $id)) {
            $errors['email'] = 'Cet email est déjà utilisé.';
        }

        if (!empty($errors)) {
            $data['id'] = $id;
            $this->render('parametres/formulaire_user', [
                'title'      => 'Modifier un utilisateur',
                'pageTitle'  => 'Modifier un utilisateur',
                'user'       => AuthMiddleware::user(),
                'csrf_token' => $this->generateCsrfToken(),
                'userEdit'   => $data,
                'errors'     => $errors,
            ]);
            return;
        }

        $updateData = [
            'nom'    => $data['nom'],
            'prenom' => $data['prenom'],
            'email'  => $data['email'],
            'role'   => $data['role'],
            'actif'  => (int) $data['actif'],
        ];

        // Changer le mot de passe seulement si fourni
        if (!empty($data['password'])) {
            if (strlen($data['password']) < 8) {
                $errors['password'] = 'Mot de passe trop court (8 car. min.).';
                $data['id'] = $id;
                $this->render('parametres/formulaire_user', [
                    'title'      => 'Modifier un utilisateur',
                    'pageTitle'  => 'Modifier un utilisateur',
                    'user'       => AuthMiddleware::user(),
                    'csrf_token' => $this->generateCsrfToken(),
                    'userEdit'   => $data,
                    'errors'     => $errors,
                ]);
                return;
            }
            $this->userModel->changePassword($id, $data['password']);
        }

        $this->userModel->update($id, $updateData);
        $this->flash('success', 'Utilisateur mis à jour.');
        Router::redirect('parametres');
    }

    // ─────────────────────────────────────────
    // POST /parametres/toggleUser/{id}
    // ─────────────────────────────────────────
    public function toggleUser(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);
        $this->requireMethod('POST');
        $this->validateCsrf();

        $id      = (int) $param;
        $userEdit = $this->userModel->findById($id);

        // Ne pas désactiver soi-même
        $me = AuthMiddleware::user();
        if ($id === (int) $me['id']) {
            $this->flash('error', 'Vous ne pouvez pas vous désactiver vous-même.');
            Router::redirect('parametres');
        }

        if ($userEdit) {
            $newActif = $userEdit['actif'] ? 0 : 1;
            $this->userModel->update($id, ['actif' => $newActif]);
            $this->flash('success', $newActif ? 'Utilisateur activé.' : 'Utilisateur désactivé.');
        }

        Router::redirect('parametres');
    }

    // ─────────────────────────────────────────
    // HELPERS PRIVÉS
    // ─────────────────────────────────────────
    private function getAllParams(): array {
        try {
            $db   = Database::getConnection();
            $stmt = $db->query("SELECT cle, valeur FROM `parametres`");
            $rows = $stmt->fetchAll();
            $p    = [];
            foreach ($rows as $row) $p[$row['cle']] = $row['valeur'];
            return $p;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function collectUserData(): array {
        return [
            'nom'      => strtoupper(trim($this->post('nom', ''))),
            'prenom'   => ucwords(strtolower(trim($this->post('prenom', '')))),
            'email'    => strtolower(trim($this->post('email', ''))),
            'role'     => $this->post('role', 'professeur'),
            'password' => $this->post('password', ''),
            'actif'    => $this->post('actif', 1),
        ];
    }

    private function validerUser(array $data): array {
        $errors = [];
        if (empty($data['nom']))    $errors['nom']    = 'Le nom est obligatoire.';
        if (empty($data['prenom'])) $errors['prenom'] = 'Le prénom est obligatoire.';
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL))
            $errors['email'] = 'Email invalide.';
        if (!in_array($data['role'], ['admin', 'professeur', 'parent']))
            $errors['role'] = 'Rôle invalide.';
        return $errors;
    }
}
