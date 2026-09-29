    <?php
    // app/controllers/DashboardController.php

    require_once ROOT_PATH . '/app/core/Controller.php';
    require_once ROOT_PATH . '/app/models/Eleve.php';
    require_once ROOT_PATH . '/app/models/Classe.php';
    require_once ROOT_PATH . '/app/models/Note.php';
    require_once ROOT_PATH . '/app/models/Paiement.php';
    require_once ROOT_PATH . '/app/models/User.php';
    require_once ROOT_PATH . '/app/models/Evenement.php';

    class DashboardController extends Controller {

        public function index(?string $param = null): void {
            AuthMiddleware::requireAuth();

            $authorization = AuthorizationService::getInstance();

            // Les comptes parents et élèves ont leur propre espace,
            // distinct du tableau de bord de pilotage de l'établissement.
            if (AuthMiddleware::hasRole(ROLE_PARENT)) {
                Router::redirect('espace-parent');
            }
            if (AuthMiddleware::hasRole(ROLE_ELEVE)) {
                Router::redirect('espace-eleve');
            }
            if (AuthMiddleware::hasRole(ROLE_PROF)) {
                $this->render('dashboard/enseignant', [
                    'title' => 'Tableau de bord enseignant',
                    'pageTitle' => 'Tableau de bord enseignant',
                    'user' => AuthMiddleware::user(),
                    'flash' => $this->getFlash(),
                    'csrf_token' => $this->generateCsrfToken(),
                    'canEvents' => $authorization->hasPermission('events.view'),
                    'canGrades' => $authorization->hasPermission('grades.view'),
                    'canAttendance' => $authorization->hasPermission('attendance.view'),
                    'canReports' => $authorization->hasPermission('report_cards.view'),
                ]);
                return;
            }

            $user         = AuthMiddleware::user();
            $eleveModel   = new Eleve();
            $classeModel  = new Classe();
            $paiementModel = new Paiement();
            $evenementModel = new Evenement();
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

            // ── Présence hebdomadaire (Lun→Ven de la semaine en cours) ──
            $presenceHebdo   = [];
            $joursLabels     = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven'];
            $lundi           = new DateTime('monday this week');
            $stmtAbs         = $db->prepare(
                "SELECT COUNT(DISTINCT eleve_id) FROM `absences` WHERE date_absence = ?"
            );
            foreach ($joursLabels as $i => $label) {
                $jour = (clone $lundi)->modify("+{$i} day");
                $dateStr = $jour->format('Y-m-d');
                $stmtAbs->execute([$dateStr]);
                $nbAbsents = (int) $stmtAbs->fetchColumn();
                $pct = $nbEleves > 0 ? round((($nbEleves - $nbAbsents) / $nbEleves) * 100) : 0;
                $presenceHebdo[] = [
                    'label' => $label,
                    'date'  => $dateStr,
                    'pct'   => max(0, min(100, $pct)),
                    'futur' => $jour > new DateTime('today'),
                ];
            }
            $joursPasses = array_filter($presenceHebdo, fn($j) => !$j['futur']);
            $presenceMoyenne = count($joursPasses) > 0
                ? round(array_sum(array_column($joursPasses, 'pct')) / count($joursPasses), 1)
                : 0;

            // ── Événements à venir ──
            $prochainsEvenements = $evenementModel->prochains(4);

            // ── Top 5 élèves (moyenne générale pondérée, trimestre en cours) ──
            $topEleves = $db->query(
                "SELECT e.id, e.nom, e.prenom, c.nom AS classe_nom,
                        ROUND(SUM(n.avg_note * m.coefficient) / NULLIF(SUM(m.coefficient), 0), 2) AS moyenne
                FROM (
                    SELECT eleve_id, matiere_id, AVG(note) AS avg_note
                    FROM `notes`
                    WHERE periode = 1
                    GROUP BY eleve_id, matiere_id
                ) n
                JOIN `matieres` m ON m.id = n.matiere_id
                JOIN `eleves` e  ON e.id = n.eleve_id
                LEFT JOIN `classes` c ON c.id = e.classe_id
                GROUP BY n.eleve_id
                HAVING moyenne IS NOT NULL
                ORDER BY moyenne DESC
                LIMIT 5"
            )->fetchAll();

            // ── Évolution des inscriptions (12 derniers mois) ──
            $inscriptionsParMois = $db->query(
                "SELECT DATE_FORMAT(created_at, '%Y-%m') AS mois,
                        DATE_FORMAT(created_at, '%b %Y') AS mois_label,
                        COUNT(*) AS total
                FROM `eleves`
                WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 11 MONTH)
                GROUP BY mois
                ORDER BY mois ASC"
            )->fetchAll();

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
                'presenceHebdo'     => $presenceHebdo,
                'presenceMoyenne'   => $presenceMoyenne,
                'prochainsEvenements' => $prochainsEvenements,
                'topEleves'           => $topEleves,
                'inscriptionsParMois' => $inscriptionsParMois,
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
