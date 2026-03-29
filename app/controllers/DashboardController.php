<?php
// app/controllers/DashboardController.php

require_once ROOT_PATH . '/app/core/Controller.php';
require_once ROOT_PATH . '/app/models/Eleve.php';
require_once ROOT_PATH . '/app/models/Classe.php';
require_once ROOT_PATH . '/app/models/Note.php';
require_once ROOT_PATH . '/app/models/Paiement.php';
require_once ROOT_PATH . '/app/models/User.php';

class DashboardController extends Controller {

    public function index(?string $param = null): void {
        AuthMiddleware::requireAuth();

        $user         = AuthMiddleware::user();
        $eleveModel   = new Eleve();
        $classeModel  = new Classe();
        $paiementModel = new Paiement();
        $db           = Database::getConnection();

        $annee   = $classeModel->anneeActive();
        $anneeId = $annee['id'] ?? 0;

        // ── KPIs ──
        $nbEleves   = (int) $db->query("SELECT COUNT(*) FROM `eleves` WHERE actif = 1")->fetchColumn();
        $nbClasses  = (int) $db->query("SELECT COUNT(*) FROM `classes`")->fetchColumn();
        $nbProfs    = (int) $db->query("SELECT COUNT(*) FROM `users` WHERE role = 'professeur' AND actif = 1")->fetchColumn();

        $statsP = $paiementModel->statistiques($anneeId);
        $totalEncaisse = (int) ($statsP['total_encaisse'] ?? 0);

        // ── Élèves par classe (graphique barres) ──
        $elevesParClasse = $eleveModel->countParClasse();

        // ── Paiements par mois (graphique ligne) ──
        $paiementsParMois = $paiementModel->parMois($anneeId);

        // ── Répartition par sexe ──
        $stmt = $db->query("SELECT sexe, COUNT(*) AS total FROM `eleves` WHERE actif=1 GROUP BY sexe");
        $sexeData = $stmt->fetchAll();

        // ── Répartition des mentions ──
        $mentions = $this->calculerMentions($db);

        // ── Derniers paiements ──
        $derniersPaiements = $db->query(
            "SELECT p.recu_numero, p.montant_fcfa, p.date_paiement, p.statut,
                    e.nom AS eleve_nom, e.prenom AS eleve_prenom
             FROM `paiements` p
             JOIN `eleves` e ON e.id = p.eleve_id
             ORDER BY p.created_at DESC LIMIT 5"
        )->fetchAll();

        // ── Derniers élèves inscrits ──
        $derniersEleves = $db->query(
            "SELECT e.nom, e.prenom, e.matricule, e.created_at, c.nom AS classe_nom
             FROM `eleves` e
             LEFT JOIN `classes` c ON c.id = e.classe_id
             WHERE e.actif = 1
             ORDER BY e.created_at DESC LIMIT 5"
        )->fetchAll();

        $this->render('dashboard/index', [
            'title'             => 'Tableau de bord',
            'pageTitle'         => 'Tableau de bord',
            'user'              => $user,
            'flash'             => $this->getFlash(),
            'csrf_token'        => $this->generateCsrfToken(),
            'annee'             => $annee,
            // KPIs
            'nbEleves'          => $nbEleves,
            'nbClasses'         => $nbClasses,
            'nbProfs'           => $nbProfs,
            'totalEncaisse'     => $totalEncaisse,
            // Graphiques
            'elevesParClasse'   => $elevesParClasse,
            'paiementsParMois'  => $paiementsParMois,
            'sexeData'          => $sexeData,
            'mentions'          => $mentions,
            // Listes récentes
            'derniersPaiements' => $derniersPaiements,
            'derniersEleves'    => $derniersEleves,
        ]);
    }

    private function calculerMentions(\PDO $db): array {
        // Calcule les mentions de tous les élèves au dernier trimestre avec notes
        $stmt = $db->query(
            "SELECT
                CASE
                    WHEN moy >= 16 THEN 'Très bien'
                    WHEN moy >= 14 THEN 'Bien'
                    WHEN moy >= 12 THEN 'Assez bien'
                    WHEN moy >= 10 THEN 'Passable'
                    ELSE 'Insuffisant'
                END AS mention,
                COUNT(*) AS total
             FROM (
                SELECT n.eleve_id,
                    ROUND(SUM(avg_note * m.coefficient) / NULLIF(SUM(m.coefficient), 0), 2) AS moy
                FROM (
                    SELECT eleve_id, matiere_id, AVG(note) AS avg_note
                    FROM `notes`
                    WHERE periode = 1
                    GROUP BY eleve_id, matiere_id
                ) n
                JOIN `matieres` m ON m.id = n.matiere_id
                GROUP BY n.eleve_id
             ) moyennes
             GROUP BY mention
             ORDER BY FIELD(mention,'Très bien','Bien','Assez bien','Passable','Insuffisant')"
        );
        return $stmt->fetchAll();
    }
}
