<?php
// app/controllers/ExportController.php

require_once ROOT_PATH . '/app/core/Controller.php';
require_once ROOT_PATH . '/app/models/Eleve.php';
require_once ROOT_PATH . '/app/models/Classe.php';
require_once ROOT_PATH . '/app/models/Note.php';
require_once ROOT_PATH . '/app/models/Paiement.php';

class ExportController extends Controller {

    private Eleve   $eleveModel;
    private Classe  $classeModel;
    private Note    $noteModel;
    private Paiement $paiementModel;

    public function __construct() {
        $this->eleveModel    = new Eleve();
        $this->classeModel   = new Classe();
        $this->noteModel     = new Note();
        $this->paiementModel = new Paiement();
    }

    // ─────────────────────────────────────────
    // GET /export — Page principale
    // ─────────────────────────────────────────
    public function index(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF]);

        $classes = $this->classeModel->toutesLesClasses();
        $annee   = $this->classeModel->anneeActive();

        $this->render('export/index', [
            'title'     => 'Export de données',
            'pageTitle' => 'Export CSV',
            'user'      => AuthMiddleware::user(),
            'flash'     => $this->getFlash(),
            'classes'   => $classes,
            'annee'     => $annee,
        ]);
    }

    // ─────────────────────────────────────────
    // GET /export/eleves?classe=X
    // Export CSV de la liste des élèves
    // ─────────────────────────────────────────
    public function eleves(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF]);

        $classeId = (int) $this->get('classe', 0);

        $db = Database::getConnection();
        $where  = 'WHERE e.actif = 1';
        $params = [];

        if ($classeId > 0) {
            $where   .= ' AND e.classe_id = ?';
            $params[] = $classeId;
        }

        $eleves = $this->queryDb(
            "SELECT e.matricule, e.nom, e.prenom,
                    e.date_naissance, e.sexe,
                    c.nom AS classe, c.niveau,
                    u.nom AS parent_nom, u.prenom AS parent_prenom,
                    u.email AS parent_email,
                    e.created_at
             FROM `eleves` e
             LEFT JOIN `classes` c ON c.id = e.classe_id
             LEFT JOIN `users`   u ON u.id = e.parent_id
             $where
             ORDER BY c.nom, e.nom, e.prenom",
            $params
        );

        $nomFichier = 'eleves_' . ($classeId ? 'classe' . $classeId : 'tous') . '_' . date('Ymd') . '.csv';

        $this->outputCsv($nomFichier, [
            'Matricule', 'Nom', 'Prénom', 'Date naissance', 'Sexe',
            'Classe', 'Niveau',
            'Nom parent', 'Prénom parent', 'Email parent',
            'Date inscription',
        ], array_map(fn($e) => [
            $e['matricule'],
            $e['nom'],
            $e['prenom'],
            $e['date_naissance'] ? date('d/m/Y', strtotime($e['date_naissance'])) : '',
            $e['sexe'] === 'M' ? 'Masculin' : 'Féminin',
            $e['classe'] ?? '',
            $e['niveau'] ?? '',
            $e['parent_nom'] ?? '',
            $e['parent_prenom'] ?? '',
            $e['parent_email'] ?? '',
            $e['created_at'] ? date('d/m/Y', strtotime($e['created_at'])) : '',
        ], $eleves));
    }

    // ─────────────────────────────────────────
    // GET /export/notes?classe=X&periode=Y
    // Export CSV des notes et moyennes
    // ─────────────────────────────────────────
    public function notes(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF]);

        $classeId = (int) $this->get('classe', 0);
        $periode  = (int) $this->get('periode', 1);

        if ($classeId <= 0) {
            $this->flash('error', 'Veuillez sélectionner une classe.');
            Router::redirect('export');
        }

        $db = Database::getConnection();

        // Récupérer les matières de la classe
        $matieres = $this->queryDb(
            "SELECT id, nom, coefficient FROM `matieres` WHERE classe_id = ? ORDER BY nom",
            [$classeId]
        );

        // Récupérer les élèves de la classe
        $eleves = $this->eleveModel->parClasse($classeId);

        if (empty($eleves) || empty($matieres)) {
            $this->flash('error', 'Aucune donnée à exporter.');
            Router::redirect('export');
        }

        // Construire les en-têtes CSV
        $headers = ['Matricule', 'Nom', 'Prénom'];
        foreach ($matieres as $m) {
            $headers[] = $m['nom'] . ' (coef.' . $m['coefficient'] . ')';
        }
        $headers[] = 'Moyenne générale /20';
        $headers[] = 'Mention';
        $headers[] = 'Rang';

        // Calculer les données pour chaque élève
        $rows = [];
        $moyennesGenerales = [];

        foreach ($eleves as $eleve) {
            $row = [$eleve['matricule'], $eleve['nom'], $eleve['prenom']];

            // Moyenne par matière
            $moyParMatiere = [];
            $moyennes = $this->noteModel->moyennesParEleve($eleve['id'], $periode);
            foreach ($moyennes as $moy) {
                $moyParMatiere[$moy['matiere_id']] = $moy['moyenne'];
            }

            foreach ($matieres as $m) {
                $row[] = isset($moyParMatiere[$m['id']])
                    ? number_format($moyParMatiere[$m['id']], 2, '.', '')
                    : '';
            }

            $moyGen = $this->noteModel->moyenneGenerale($eleve['id'], $periode);
            $row[] = $moyGen > 0 ? number_format($moyGen, 2, '.', '') : '';
            $row[] = $this->getMention($moyGen);

            $moyennesGenerales[$eleve['id']] = $moyGen;
            $rows[$eleve['id']] = $row;
        }

        // Calculer les rangs
        arsort($moyennesGenerales);
        $rang = 1;
        foreach ($moyennesGenerales as $eleveId => $moy) {
            $rows[$eleveId][] = $moy > 0 ? $rang : '—';
            $rang++;
        }

        $periodeLabel = ['', '1T', '2T', '3T'][$periode] ?? $periode;
        $nomFichier   = 'notes_classe' . $classeId . '_' . $periodeLabel . '_' . date('Ymd') . '.csv';

        $this->outputCsv($nomFichier, $headers, array_values($rows));
    }

    // ─────────────────────────────────────────
    // GET /export/paiements?annee=X
    // Export CSV des paiements
    // ─────────────────────────────────────────
    public function paiements(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);

        $annee   = $this->classeModel->anneeActive();
        $anneeId = $annee['id'] ?? 0;

        $data = $this->queryDb(
            "SELECT p.recu_numero, e.matricule, e.nom AS eleve_nom, e.prenom AS eleve_prenom,
                    c.nom AS classe,
                    p.montant_fcfa, p.date_paiement, p.mode_paiement, p.statut,
                    p.commentaire,
                    u.nom AS createur_nom, u.prenom AS createur_prenom
             FROM `paiements` p
             JOIN `eleves` e  ON e.id = p.eleve_id
             JOIN `classes` c ON c.id = e.classe_id
             JOIN `users`   u ON u.id = p.created_by
             WHERE p.annee_id = ?
             ORDER BY p.date_paiement DESC",
            [$anneeId]
        );

        $modeLabel = ['especes' => 'Espèces', 'mobile_money' => 'Mobile Money', 'virement' => 'Virement'];
        $statutLabel = ['paye' => 'Payé', 'partiel' => 'Partiel', 'annule' => 'Annulé'];

        $nomFichier = 'paiements_' . ($annee['libelle'] ?? date('Y')) . '_' . date('Ymd') . '.csv';

        $this->outputCsv($nomFichier, [
            'N° Reçu', 'Matricule', 'Nom élève', 'Prénom élève', 'Classe',
            'Montant (FCFA)', 'Date paiement', 'Mode', 'Statut', 'Commentaire',
            'Enregistré par',
        ], array_map(fn($p) => [
            $p['recu_numero'],
            $p['matricule'],
            $p['eleve_nom'],
            $p['eleve_prenom'],
            $p['classe'],
            $p['montant_fcfa'],
            date('d/m/Y', strtotime($p['date_paiement'])),
            $modeLabel[$p['mode_paiement']] ?? $p['mode_paiement'],
            $statutLabel[$p['statut']] ?? $p['statut'],
            $p['commentaire'] ?? '',
            $p['createur_prenom'] . ' ' . $p['createur_nom'],
        ], $data));
    }

    // ─────────────────────────────────────────
    // HELPERS PRIVÉS
    // ─────────────────────────────────────────

    private function outputCsv(string $filename, array $headers, array $rows): void {
        // Headers HTTP pour téléchargement
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');

        $output = fopen('php://output', 'w');

        // BOM UTF-8 pour Excel (Windows)
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // En-têtes
        fputcsv($output, $headers, ';');

        // Données
        foreach ($rows as $row) {
            fputcsv($output, $row, ';');
        }

        fclose($output);
        exit;
    }

    private function queryDb(string $sql, array $params = []): array {
        $db   = Database::getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function getMention(float $moy): string {
        if ($moy >= 16) return 'Très bien';
        if ($moy >= 14) return 'Bien';
        if ($moy >= 12) return 'Assez bien';
        if ($moy >= 10) return 'Passable';
        return $moy > 0 ? 'Insuffisant' : '—';
    }
}
