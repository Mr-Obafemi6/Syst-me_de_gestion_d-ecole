<?php
// app/controllers/EspaceEleveController.php — Portail dédié aux comptes élèves

require_once ROOT_PATH . '/app/core/Controller.php';
require_once ROOT_PATH . '/app/models/Eleve.php';
require_once ROOT_PATH . '/app/models/Note.php';
require_once ROOT_PATH . '/app/models/Absence.php';
require_once ROOT_PATH . '/app/models/Paiement.php';
require_once ROOT_PATH . '/app/models/Classe.php';

class EspaceEleveController extends Controller {

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

    /**
     * Récupère la fiche élève liée au compte connecté, ou redirige.
     */
    private function moiOuRedirect(): array {
        $user  = AuthMiddleware::user();
        $eleve = $this->eleveModel->findByUserId((int) $user['id']);

        if (!$eleve) {
            $this->flash('error', 'Aucune fiche élève n\'est associée à ce compte. Contactez l\'administration.');
            Router::redirect('dashboard');
            exit;
        }

        return $this->eleveModel->ficheComplete((int) $eleve['id']);
    }

    // ─────────────────────────────────────────
    // GET /espace-eleve — Mon profil
    // ─────────────────────────────────────────
    public function index(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ELEVE);

        $eleve   = $this->moiOuRedirect();
        $annee   = $this->classeModel->anneeActive();
        $anneeId = $annee['id'] ?? 0;

        $moyGenerales = [];
        for ($p = 1; $p <= 3; $p++) {
            $moyGenerales[$p] = $this->noteModel->moyenneGenerale((int) $eleve['id'], $p);
        }
        $stats     = $this->absenceModel->statsParEleve((int) $eleve['id']);
        $totalPaye = $anneeId ? $this->paiementModel->totalParEleve((int) $eleve['id'], $anneeId) : 0;

        $this->render('espace_eleve/index', [
            'title'         => 'Mon espace',
            'pageTitle'     => 'Mon espace élève',
            'user'          => AuthMiddleware::user(),
            'flash'         => $this->getFlash(),
            'csrf_token'    => $this->generateCsrfToken(),
            'eleve'         => $eleve,
            'moyGenerales'  => $moyGenerales,
            'stats'         => $stats,
            'totalPaye'     => $totalPaye,
            'annee'         => $annee,
        ]);
    }

    // ─────────────────────────────────────────
    // GET /espace-eleve/paiements — Mon historique de paiements
    // ─────────────────────────────────────────
    public function paiements(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ELEVE);

        $eleve     = $this->moiOuRedirect();
        $annee     = $this->classeModel->anneeActive();
        $paiements = $this->paiementModel->parEleve((int) $eleve['id']);
        $totalPaye = $annee ? $this->paiementModel->totalParEleve((int) $eleve['id'], $annee['id']) : 0;

        $this->render('espace_eleve/paiements', [
            'title'      => 'Mes paiements',
            'pageTitle'  => 'Mes paiements',
            'user'       => AuthMiddleware::user(),
            'flash'      => $this->getFlash(),
            'eleve'      => $eleve,
            'paiements'  => $paiements,
            'totalPaye'  => $totalPaye,
            'annee'      => $annee,
        ]);
    }
}
