<?php
// app/controllers/EspaceParentController.php — Portail dédié aux comptes parents

require_once ROOT_PATH . '/app/core/Controller.php';
require_once ROOT_PATH . '/app/models/Eleve.php';
require_once ROOT_PATH . '/app/models/Note.php';
require_once ROOT_PATH . '/app/models/Absence.php';
require_once ROOT_PATH . '/app/models/Paiement.php';
require_once ROOT_PATH . '/app/models/Classe.php';

class EspaceParentController extends Controller {

    private Eleve    $eleveModel;
    private Note     $noteModel;
    private Absence  $absenceModel;
    private Paiement $paiementModel;
    private Classe   $classeModel;

    public function __construct() {
        $this->eleveModel    = new Eleve();
        $this->noteModel     = new Note();
        $this->absenceModel  = new Absence();
        $this->paiementModel = new Paiement();
        $this->classeModel   = new Classe();
    }

    // ─────────────────────────────────────────
    // GET /espace-parent — Mes enfants
    // ─────────────────────────────────────────
    public function index(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_PARENT);

        $user     = AuthMiddleware::user();
        $enfants  = $this->eleveModel->parParent((int) $user['id']);
        $annee    = $this->classeModel->anneeActive();
        $anneeId  = $annee['id'] ?? 0;

        // Résumé rapide par enfant : moyenne, absences, solde
        $resumes = [];
        foreach ($enfants as $enfant) {
            $moyGen = $this->noteModel->moyenneGenerale((int) $enfant['id'], 1);
            $stats  = $this->absenceModel->statsParEleve((int) $enfant['id']);
            $totalPaye = $anneeId ? $this->paiementModel->totalParEleve((int) $enfant['id'], $anneeId) : 0;

            $resumes[] = [
                'eleve'       => $enfant,
                'moyenne'     => $moyGen,
                'absences'    => $stats['total'] ?? 0,
                'total_paye'  => $totalPaye,
            ];
        }

        $this->render('espace_parent/index', [
            'title'      => 'Mon espace parent',
            'pageTitle'  => 'Mes enfants',
            'user'       => $user,
            'flash'      => $this->getFlash(),
            'csrf_token' => $this->generateCsrfToken(),
            'resumes'    => $resumes,
            'annee'      => $annee,
        ]);
    }

    // ─────────────────────────────────────────
    // GET /espace-parent/paiements/{eleveId} — Historique des paiements d'un enfant
    // ─────────────────────────────────────────
    public function paiements(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_PARENT);

        $eleveId = (int) $param;
        $user    = AuthMiddleware::user();
        $eleve   = $this->eleveModel->ficheComplete($eleveId);

        if (!$eleve || $eleve['parent_id'] != $user['id']) {
            $this->flash('error', 'Élève introuvable.');
            Router::redirect('espace-parent');
        }

        $annee     = $this->classeModel->anneeActive();
        $paiements = $this->paiementModel->parEleve($eleveId);
        $totalPaye = $annee ? $this->paiementModel->totalParEleve($eleveId, $annee['id']) : 0;

        $this->render('espace_parent/paiements', [
            'title'      => 'Paiements — ' . $eleve['prenom'] . ' ' . $eleve['nom'],
            'pageTitle'  => 'Historique des paiements',
            'user'       => $user,
            'flash'      => $this->getFlash(),
            'eleve'      => $eleve,
            'paiements'  => $paiements,
            'totalPaye'  => $totalPaye,
            'annee'      => $annee,
        ]);
    }
}
