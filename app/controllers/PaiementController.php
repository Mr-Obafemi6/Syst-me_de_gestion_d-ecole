<?php
// app/controllers/PaiementController.php

require_once ROOT_PATH . '/app/core/Controller.php';
require_once ROOT_PATH . '/app/models/Paiement.php';
require_once ROOT_PATH . '/app/models/Eleve.php';
require_once ROOT_PATH . '/app/models/Classe.php';

class PaiementController extends Controller {

    private Paiement $paiementModel;
    private Eleve    $eleveModel;
    private Classe   $classeModel;

    public function __construct() {
        $this->paiementModel = new Paiement();
        $this->eleveModel    = new Eleve();
        $this->classeModel   = new Classe();
    }

    // ─────────────────────────────────────────
    // GET /paiements — Liste paginée
    // ─────────────────────────────────────────
    public function index(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);

        $page    = max(1, (int) $this->get('page', 1));
        $eleveId = (int) $this->get('eleve', 0);
        $statut  = $this->get('statut', '');

        $annee      = $this->classeModel->anneeActive();
        $anneeId    = $annee['id'] ?? 0;
        $pagination = $this->paiementModel->listerAvecDetails($page, $eleveId, $statut);
        $stats      = $this->paiementModel->statistiques($anneeId);
        $parMois    = $this->paiementModel->parMois($anneeId);

        $this->render('paiements/liste', [
            'title'      => 'Paiements',
            'pageTitle'  => 'Paiements de scolarité',
            'user'       => AuthMiddleware::user(),
            'flash'      => $this->getFlash(),
            'pagination' => $pagination,
            'stats'      => $stats,
            'parMois'    => $parMois,
            'annee'      => $annee,
            'eleveId'    => $eleveId,
            'statut'     => $statut,
            'csrf_token' => $this->generateCsrfToken(),
        ]);
    }

    // ─────────────────────────────────────────
    // GET /paiements/ajouter?eleve=X
    // ─────────────────────────────────────────
    public function ajouter(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);

        $eleveId = (int) $this->get('eleve', 0);
        $eleve   = $eleveId ? $this->eleveModel->ficheComplete($eleveId) : null;
        $annee   = $this->classeModel->anneeActive();

        // Calculer le total déjà payé
        $totalPaye = 0;
        if ($eleve && $annee) {
            $totalPaye = $this->paiementModel->totalParEleve($eleveId, $annee['id']);
        }

        $this->render('paiements/formulaire', [
            'title'      => 'Enregistrer un paiement',
            'pageTitle'  => 'Nouveau paiement',
            'user'       => AuthMiddleware::user(),
            'csrf_token' => $this->generateCsrfToken(),
            'eleve'      => $eleve,
            'annee'      => $annee,
            'totalPaye'  => $totalPaye,
            'errors'     => [],
            'data'       => [],
        ]);
    }

    // ─────────────────────────────────────────
    // POST /paiements/store
    // ─────────────────────────────────────────
    public function store(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);
        $this->requireMethod('POST');
        $this->validateCsrf();

        $user    = AuthMiddleware::user();
        $annee   = $this->classeModel->anneeActive();
        $eleveId = (int) $this->post('eleve_id', 0);
        $eleve   = $eleveId ? $this->eleveModel->ficheComplete($eleveId) : null;

        $data = [
            'eleve_id'       => $eleveId,
            'montant_fcfa'   => (int) str_replace([' ', ','], '', $this->post('montant_fcfa', '0')),
            'date_paiement'  => $this->post('date_paiement', date('Y-m-d')),
            'mode_paiement'  => $this->post('mode_paiement', 'especes'),
            'statut'         => $this->post('statut', 'paye'),
            'commentaire'    => trim($this->post('commentaire', '')),
            'annee_id'       => $annee['id'] ?? 0,
            'created_by'     => $user['id'],
        ];

        $errors = $this->valider($data, $eleve);

        if (!empty($errors)) {
            $totalPaye = $eleve ? $this->paiementModel->totalParEleve($eleveId, $annee['id']) : 0;
            $this->render('paiements/formulaire', [
                'title'      => 'Enregistrer un paiement',
                'pageTitle'  => 'Nouveau paiement',
                'user'       => $user,
                'csrf_token' => $this->generateCsrfToken(),
                'eleve'      => $eleve,
                'annee'      => $annee,
                'totalPaye'  => $totalPaye,
                'errors'     => $errors,
                'data'       => $data,
            ]);
            return;
        }

        // Générer le numéro de reçu
        $data['recu_numero'] = $this->paiementModel->genererNumeroRecu();

        $id = $this->paiementModel->insert($data);

        $this->flash('success',
            'Paiement enregistré. Reçu N° : ' . $data['recu_numero']
        );
        Router::redirect('paiements/recu/' . $id);
    }

    // ─────────────────────────────────────────
    // GET /paiements/recu/{id} — Reçu imprimable
    // ─────────────────────────────────────────
    public function recu(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);

        $id       = (int) $param;
        $paiement = $this->paiementModel->avecDetails($id);

        if (!$paiement) {
            $this->flash('error', 'Paiement introuvable.');
            Router::redirect('paiements');
        }

        $params    = $this->getParametres();
        $totalPaye = $this->paiementModel->totalParEleve(
            $paiement['eleve_id'],
            $paiement['annee_id']
        );

        $this->render('paiements/recu', [
            'title'      => 'Reçu ' . $paiement['recu_numero'],
            'pageTitle'  => 'Reçu de paiement',
            'user'       => AuthMiddleware::user(),
            'flash'      => null,
            'paiement'   => $paiement,
            'totalPaye'  => $totalPaye,
            'params'     => $params,
        ], 'recu_print');
    }

    // ─────────────────────────────────────────
    // POST /paiements/annuler/{id}
    // ─────────────────────────────────────────
    public function annuler(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);
        $this->requireMethod('POST');
        $this->validateCsrf();

        $id = (int) $param;
        $this->paiementModel->update($id, ['statut' => 'annule']);

        $this->flash('success', 'Paiement annulé.');
        Router::redirect('paiements');
    }

    // ─────────────────────────────────────────
    // HELPERS PRIVÉS
    // ─────────────────────────────────────────
    private function valider(array $data, ?array $eleve): array {
        $errors = [];

        if (!$eleve)
            $errors['eleve_id'] = 'Veuillez sélectionner un élève valide.';

        if ($data['montant_fcfa'] <= 0)
            $errors['montant_fcfa'] = 'Le montant doit être supérieur à 0 FCFA.';

        if ($data['montant_fcfa'] > 10000000)
            $errors['montant_fcfa'] = 'Montant trop élevé.';

        if (empty($data['date_paiement']))
            $errors['date_paiement'] = 'La date est obligatoire.';

        if (!in_array($data['mode_paiement'], ['especes', 'mobile_money', 'virement']))
            $errors['mode_paiement'] = 'Mode de paiement invalide.';

        if (!$data['annee_id'])
            $errors['annee_id'] = 'Aucune année scolaire active.';

        return $errors;
    }

    private function getParametres(): array {
        try {
            $db   = \Database::getConnection();
            $stmt = $db->query("SELECT cle, valeur FROM `parametres`");
            $rows = $stmt->fetchAll();
            $p    = [];
            foreach ($rows as $row) $p[$row['cle']] = $row['valeur'];
            return $p;
        } catch (\Exception $e) {
            return [];
        }
    }
}
