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
        $groupes    = $this->classeModel->classesParCycle();
        $anneeActive = $this->classeModel->anneeActive();

        $totalEleves = array_sum(array_column($classes, 'nb_eleves'));
        $statsClasses = [
            'total'        => count($classes),
            'total_eleves' => $totalEleves,
            'moyenne'      => count($classes) > 0 ? round($totalEleves / count($classes), 1) : 0,
            'total_matieres' => array_sum(array_column($classes, 'nb_matieres')),
        ];

        $this->render('classes/liste', [
            'title'      => 'Classes',
            'pageTitle'  => 'Gestion des classes',
            'user'       => AuthMiddleware::user(),
            'flash'      => $this->getFlash(),
            'classes'    => $classes,
            'groupes'    => $groupes,
            'annee'      => $anneeActive,
            'statsClasses' => $statsClasses,
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
        $niveauxGroupes = $this->classeModel->niveauxParCycle();
        $profs = $this->classeModel->tousLesProfesseurs();

        $this->render('classes/formulaire', [
            'title'      => 'Ajouter une classe',
            'pageTitle'  => 'Ajouter une classe',
            'user'       => AuthMiddleware::user(),
            'csrf_token' => $this->generateCsrfToken(),
            'annees'     => $annees,
            'niveauxGroupes' => $niveauxGroupes,
            'profs'      => $profs,
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
            $niveauxGroupes = $this->classeModel->niveauxParCycle();
            $profs = $this->classeModel->tousLesProfesseurs();
            $this->render('classes/formulaire', [
                'title'      => 'Ajouter une classe',
                'pageTitle'  => 'Ajouter une classe',
                'user'       => AuthMiddleware::user(),
                'csrf_token' => $this->generateCsrfToken(),
                'annees'     => $annees,
                'niveauxGroupes' => $niveauxGroupes,
                'profs'      => $profs,
                'classe'     => $data,
                'errors'     => $errors,
            ]);
            return;
        }

        $this->classeModel->insert($data);
        $libelle = trim($data['niveau'] . ' ' . $data['nom']);
        $this->flash('success', 'Classe "' . $libelle . '" créée avec succès.');
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
        $niveauxGroupes = $this->classeModel->niveauxParCycle();
        $profs = $this->classeModel->tousLesProfesseurs();

        $this->render('classes/formulaire', [
            'title'      => 'Modifier ' . $classe['nom'],
            'pageTitle'  => 'Modifier une classe',
            'user'       => AuthMiddleware::user(),
            'csrf_token' => $this->generateCsrfToken(),
            'annees'     => $annees,
            'niveauxGroupes' => $niveauxGroupes,
            'profs'      => $profs,
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
            $niveauxGroupes = $this->classeModel->niveauxParCycle();
            $profs = $this->classeModel->tousLesProfesseurs();
            $data['id'] = $id;
            $this->render('classes/formulaire', [
                'title'      => 'Modifier une classe',
                'pageTitle'  => 'Modifier une classe',
                'user'       => AuthMiddleware::user(),
                'csrf_token' => $this->generateCsrfToken(),
                'annees'     => $annees,
                'niveauxGroupes' => $niveauxGroupes,
                'profs'      => $profs,
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
        $catalogue = $this->matiereModel->catalogue();

        $this->render('classes/formulaire_matiere', [
            'title'      => 'Ajouter une matière',
            'pageTitle'  => 'Ajouter une matière — ' . ($classe['libelle_complete'] ?? $classe['nom']),
            'user'       => AuthMiddleware::user(),
            'csrf_token' => $this->generateCsrfToken(),
            'classe'     => $classe,
            'matiere'    => null,
            'profs'      => $profs,
            'catalogue'  => $catalogue,
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
        $classe      = $this->classeModel->avecDetails($classeId);

        if (!$classe) {
            $this->flash('error', 'Classe introuvable.');
            Router::redirect('classes');
        }

        $matiereId   = (int) $this->post('matiere_id', 0);
        $coefficient = (float) str_replace(',', '.', (string) $this->post('coefficient', '1'));
        $profId      = $this->post('prof_id') !== '' ? (int) $this->post('prof_id') : null;
        $nom         = trim((string) $this->post('nom', ''));

        $errors = [];
        if ($matiereId <= 0 && empty($nom)) {
            $errors['nom'] = 'Choisissez une matière du catalogue ou saisissez un nouveau nom.';
        }
        if ($coefficient <= 0 || $coefficient > 20) {
            $errors['coefficient'] = 'Le coefficient doit être entre 0.5 et 20.';
        }

        if (!empty($errors)) {
            $profs = $this->classeModel->tousLesProfesseurs();
            $catalogue = $this->matiereModel->catalogue();
            $this->render('classes/formulaire_matiere', [
                'title'      => 'Ajouter une matière',
                'pageTitle'  => 'Ajouter une matière — ' . ($classe['libelle_complete'] ?? $classe['nom']),
                'user'       => AuthMiddleware::user(),
                'csrf_token' => $this->generateCsrfToken(),
                'classe'     => $classe,
                'matiere'    => ['nom' => $nom, 'coefficient' => $coefficient, 'prof_id' => $profId, 'matiere_id' => $matiereId],
                'profs'      => $profs,
                'catalogue'  => $catalogue,
                'errors'     => $errors,
            ]);
            return;
        }

        if ($matiereId <= 0) {
            $matiereId = $this->matiereModel->insert([
                'nom' => $nom,
                'coefficient' => $coefficient,
                'statut' => 1,
                'prof_id' => $profId,
            ]);
        } elseif ($profId) {
            $this->matiereModel->update($matiereId, ['prof_id' => $profId]);
        }

        $this->matiereModel->assignerAClasse($matiereId, $classeId, $coefficient);
        $this->flash('success', 'Matière assignée à la classe.');
        Router::redirect('classes/detail/' . $classeId);
    }

    // ─────────────────────────────────────────
    // POST /classes/supprimerMatiere/{id}
    // ─────────────────────────────────────────
    public function supprimerMatiere(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);
        $this->requireMethod('POST');
        $this->validateCsrf();

        $matiereId = (int) $this->post('matiere_id', (int) $param);
        $classeId  = (int) $this->post('classe_id', 0);

        if ($classeId <= 0) {
            $matiere = $this->matiereModel->findById($matiereId);
            $classeId = (int) ($matiere['classe_id'] ?? 0);
        }

        $this->matiereModel->retirerDeClasse($matiereId, $classeId);
        $this->flash('success', 'Matière retirée de la classe.');
        Router::redirect('classes/detail/' . $classeId);
    }

    // ─────────────────────────────────────────
    // HELPERS PRIVÉS
    // ─────────────────────────────────────────
    private function collectFormData(): array {
        $niveauId = (int) $this->post('niveau_id', 0);
        $nom = trim($this->post('nom', ''));
        $niveauNom = trim($this->post('niveau', ''));

        if ($niveauId > 0) {
            $niveau = $this->classeModel->findNiveauById($niveauId);
            if ($niveau) {
                $niveauNom = $niveau['nom'];
            }
        }

        return [
            'niveau_id'         => $niveauId,
            'nom'               => $nom,
            'niveau'            => $niveauNom,
            'annee_scolaire_id' => (int) $this->post('annee_scolaire_id', 0),
            'enseignant_id'     => $this->post('enseignant_id') !== '' ? (int) $this->post('enseignant_id', 0) : null,
            'effectif_maximum'  => (int) $this->post('effectif_maximum', 40),
            'statut'            => (int) $this->post('statut', 1),
        ];
    }

    private function valider(array $data): array {
        $errors = [];
        if (empty($data['nom']))
            $errors['nom'] = 'La division est obligatoire (ex. A, B, C).';
        if ($data['niveau_id'] <= 0)
            $errors['niveau'] = 'Le niveau / série est obligatoire.';
        if ($data['annee_scolaire_id'] <= 0)
            $errors['annee_scolaire_id'] = 'Veuillez choisir une année scolaire.';
        if ($data['effectif_maximum'] <= 0)
            $errors['effectif_maximum'] = 'L’effectif maximum doit être supérieur à 0.';
        return $errors;
    }
}
