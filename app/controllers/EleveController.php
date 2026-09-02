<?php
// app/controllers/EleveController.php

require_once ROOT_PATH . '/app/core/Controller.php';
require_once ROOT_PATH . '/app/models/Eleve.php';
require_once ROOT_PATH . '/app/models/Classe.php';
require_once ROOT_PATH . '/app/models/User.php';

class EleveController extends Controller {

    private Eleve  $eleveModel;
    private Classe $classeModel;
    private User   $userModel;

    public function __construct() {
        $this->eleveModel  = new Eleve();
        $this->classeModel = new Classe();
        $this->userModel   = new User();
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

        $db = Database::getConnection();
        $statsEleves = [
            'total'   => (int) $db->query("SELECT COUNT(*) FROM `eleves` WHERE actif = 1")->fetchColumn(),
            'garcons' => (int) $db->query("SELECT COUNT(*) FROM `eleves` WHERE actif = 1 AND sexe = 'M'")->fetchColumn(),
            'filles'  => (int) $db->query("SELECT COUNT(*) FROM `eleves` WHERE actif = 1 AND sexe = 'F'")->fetchColumn(),
            'nouveaux'=> (int) $db->query("SELECT COUNT(*) FROM `eleves` WHERE actif = 1 AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn(),
        ];

        $this->render('eleves/liste', [
            'title'      => 'Élèves',
            'pageTitle'  => 'Gestion des élèves',
            'user'       => AuthMiddleware::user(),
            'flash'      => $this->getFlash(),
            'pagination'  => $pagination,
            'classes'     => $classes,
            'statsEleves' => $statsEleves,
            'csrf_token'  => $this->generateCsrfToken(),
        ]);
    }

    // ─────────────────────────────────────────
    // GET /eleves/fiche/{id}
    // ─────────────────────────────────────────
    public function fiche(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF, ROLE_PARENT, ROLE_ELEVE]);

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
                Router::redirect('espace-parent');
            }
        }

        // Élève ne peut voir que sa propre fiche
        if (AuthMiddleware::hasRole(ROLE_ELEVE)) {
            $user = AuthMiddleware::user();
            if ($eleve['user_id'] != $user['id']) {
                Router::redirect('espace-eleve');
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
        $parents = $this->userModel->findBy(['role' => ROLE_PARENT, 'actif' => 1]);

        $this->render('eleves/formulaire', [
            'title'      => 'Ajouter un élève',
            'pageTitle'  => 'Ajouter un élève',
            'user'       => AuthMiddleware::user(),
            'flash'      => null,
            'csrf_token' => $this->generateCsrfToken(),
            'classes'    => $classes,
            'parents'    => $parents,
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

        // Traite la sélection / création du compte parent
        [$parentId, $parentErrors, $parentInfo] = $this->traiterParent();
        $errors = array_merge($errors, $parentErrors);
        $data['parent_id'] = $parentId;

        if (!empty($errors)) {
            $classes = $this->classeModel->toutesLesClasses();
            $parents = $this->userModel->findBy(['role' => ROLE_PARENT, 'actif' => 1]);
            $this->render('eleves/formulaire', [
                'title'      => 'Ajouter un élève',
                'pageTitle'  => 'Ajouter un élève',
                'user'       => AuthMiddleware::user(),
                'flash'      => null,
                'csrf_token' => $this->generateCsrfToken(),
                'classes'    => $classes,
                'parents'    => $parents,
                'eleve'      => $data,
                'errors'     => $errors,
            ]);
            return;
        }

        $data['matricule'] = $this->eleveModel->genererMatricule();
        $id = $this->eleveModel->insert($data);

        $msg = 'Élève ajouté avec succès. Matricule : ' . $data['matricule'];
        if ($parentInfo) {
            $msg .= ' | Compte parent créé — identifiant : ' . $parentInfo['email']
                  . ' / mot de passe temporaire : ' . $parentInfo['password'];
        }

        $this->flash('success', $msg);
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
        $parents = $this->userModel->findBy(['role' => ROLE_PARENT, 'actif' => 1]);

        $this->render('eleves/formulaire', [
            'title'      => 'Modifier ' . $eleve['prenom'] . ' ' . $eleve['nom'],
            'pageTitle'  => 'Modifier un élève',
            'user'       => AuthMiddleware::user(),
            'flash'      => null,
            'csrf_token' => $this->generateCsrfToken(),
            'classes'    => $classes,
            'parents'    => $parents,
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

        [$parentId, $parentErrors, $parentInfo] = $this->traiterParent();
        $errors = array_merge($errors, $parentErrors);
        $data['parent_id'] = $parentId;

        if (!empty($errors)) {
            $classes = $this->classeModel->toutesLesClasses();
            $parents = $this->userModel->findBy(['role' => ROLE_PARENT, 'actif' => 1]);
            $data['id']        = $id;
            $data['matricule'] = $eleve['matricule'];
            $this->render('eleves/formulaire', [
                'title'      => 'Modifier un élève',
                'pageTitle'  => 'Modifier un élève',
                'user'       => AuthMiddleware::user(),
                'flash'      => null,
                'csrf_token' => $this->generateCsrfToken(),
                'classes'    => $classes,
                'parents'    => $parents,
                'eleve'      => $data,
                'errors'     => $errors,
            ]);
            return;
        }

        $this->eleveModel->update($id, $data);

        $msg = 'Élève mis à jour avec succès.';
        if ($parentInfo) {
            $msg .= ' | Compte parent créé — identifiant : ' . $parentInfo['email']
                  . ' / mot de passe temporaire : ' . $parentInfo['password'];
        }

        $this->flash('success', $msg);
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
    // POST /eleves/creerCompte/{id}
    // Crée (ou réinitialise) le compte de connexion personnel de l'élève
    // ─────────────────────────────────────────
    public function creerCompte(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);
        $this->requireMethod('POST');
        $this->validateCsrf();

        $id    = (int) $param;
        $eleve = $this->eleveModel->findById($id);

        if (!$eleve) {
            $this->flash('error', 'Élève introuvable.');
            Router::redirect('eleves');
        }

        $emailSaisi = trim((string) $this->post('compte_email', ''));
        $email = $emailSaisi !== ''
            ? strtolower($emailSaisi)
            : $this->eleveModel->genererIdentifiantCompte($eleve['matricule']);

        if (!empty($eleve['user_id'])) {
            // Compte déjà existant : on réinitialise simplement le mot de passe
            $password = $this->userModel->genererMotDePasseTemporaire();
            $this->userModel->changePassword((int) $eleve['user_id'], $password);
            $this->flash('success',
                'Mot de passe réinitialisé pour ' . $eleve['prenom'] . ' ' . $eleve['nom'] .
                ' — nouveau mot de passe temporaire : ' . $password
            );
            Router::redirect('eleves/fiche/' . $id);
            return;
        }

        if ($this->userModel->emailExists($email)) {
            $this->flash('error', 'Cet identifiant (email) est déjà utilisé par un autre compte.');
            Router::redirect('eleves/fiche/' . $id);
            return;
        }

        $password = $this->userModel->genererMotDePasseTemporaire();
        $userId = $this->userModel->createUser([
            'nom'      => $eleve['nom'],
            'prenom'   => $eleve['prenom'],
            'email'    => $email,
            'password' => $password,
            'role'     => ROLE_ELEVE,
            'actif'    => 1,
        ]);

        $this->eleveModel->lierCompte($id, $userId);

        $this->flash('success',
            'Compte élève créé pour ' . $eleve['prenom'] . ' ' . $eleve['nom'] .
            ' — identifiant : ' . $email . ' / mot de passe temporaire : ' . $password
        );
        Router::redirect('eleves/fiche/' . $id);
    }

    // ─────────────────────────────────────────
    // POST /eleves/desactiverCompte/{id}
    // Désactive le compte de connexion de l'élève (sans le supprimer)
    // ─────────────────────────────────────────
    public function desactiverCompte(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);
        $this->requireMethod('POST');
        $this->validateCsrf();

        $id    = (int) $param;
        $eleve = $this->eleveModel->findById($id);

        if ($eleve && !empty($eleve['user_id'])) {
            $this->userModel->update((int) $eleve['user_id'], ['actif' => 0]);
            $this->flash('success', 'Compte de connexion désactivé.');
        }

        Router::redirect('eleves/fiche/' . $id);
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
            'actif'          => 1,
        ];
    }

    /**
     * Gère le bloc "Parent / Tuteur" du formulaire élève :
     * - parent_mode = 'existant' → réutilise un compte parent déjà créé
     * - parent_mode = 'nouveau'  → crée un nouveau compte parent à la volée
     * - parent_mode = 'aucun'    → pas de parent lié
     *
     * Retourne [parent_id (?int), errors (array), parentInfo (?array email/password si créé)]
     */
    private function traiterParent(): array {
        $mode = $this->post('parent_mode', 'aucun');
        $errors = [];
        $parentInfo = null;

        if ($mode === 'existant') {
            $parentId = (int) $this->post('parent_id', 0);
            return [$parentId > 0 ? $parentId : null, $errors, $parentInfo];
        }

        if ($mode === 'nouveau') {
            $nom       = strtoupper(trim($this->post('parent_nouveau_nom', '')));
            $prenom    = ucwords(strtolower(trim($this->post('parent_nouveau_prenom', ''))));
            $email     = strtolower(trim($this->post('parent_nouveau_email', '')));
            $telephone = trim($this->post('parent_nouveau_telephone', ''));

            if (empty($nom) || empty($prenom) || empty($email)) {
                $errors['parent_nouveau'] = 'Nom, prénom et email du parent sont obligatoires pour créer un compte.';
                return [null, $errors, $parentInfo];
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['parent_nouveau'] = 'Adresse email du parent invalide.';
                return [null, $errors, $parentInfo];
            }

            if ($this->userModel->emailExists($email)) {
                $errors['parent_nouveau'] = 'Un compte existe déjà avec cet email. Choisissez "Parent déjà enregistré".';
                return [null, $errors, $parentInfo];
            }

            $password = $this->userModel->genererMotDePasseTemporaire();
            $parentId = $this->userModel->createUser([
                'nom'       => $nom,
                'prenom'    => $prenom,
                'email'     => $email,
                'telephone' => $telephone ?: null,
                'password'  => $password,
                'role'      => ROLE_PARENT,
                'actif'     => 1,
            ]);

            $parentInfo = ['email' => $email, 'password' => $password];
            return [$parentId, $errors, $parentInfo];
        }

        // mode === 'aucun'
        return [null, $errors, $parentInfo];
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
