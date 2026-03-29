<?php
// app/controllers/BulletinController.php

require_once ROOT_PATH . '/app/core/Controller.php';
require_once ROOT_PATH . '/app/models/Eleve.php';
require_once ROOT_PATH . '/app/models/Note.php';
require_once ROOT_PATH . '/app/models/Classe.php';
require_once ROOT_PATH . '/app/models/Matiere.php';

class BulletinController extends Controller {

    private Eleve   $eleveModel;
    private Note    $noteModel;
    private Classe  $classeModel;
    private Matiere $matiereModel;

    public function __construct() {
        $this->eleveModel   = new Eleve();
        $this->noteModel    = new Note();
        $this->classeModel  = new Classe();
        $this->matiereModel = new Matiere();
    }

    // ─────────────────────────────────────────
    // GET /bulletins — Sélection classe/période
    // ─────────────────────────────────────────
    public function index(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF, ROLE_PARENT]);

        $classes = $this->classeModel->classesActives();

        $this->render('bulletins/index', [
            'title'     => 'Bulletins',
            'pageTitle' => 'Bulletins de notes',
            'user'      => AuthMiddleware::user(),
            'flash'     => $this->getFlash(),
            'classes'   => $classes,
        ]);
    }

    // ─────────────────────────────────────────
    // GET /bulletins/eleve/{id}?periode=X
    // Bulletin d'un élève — version imprimable
    // ─────────────────────────────────────────
    public function eleve(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF, ROLE_PARENT]);

        $id      = (int) $param;
        $periode = (int) $this->get('periode', 1);
        $print   = $this->get('print', '0') === '1';

        $eleve = $this->eleveModel->ficheComplete($id);
        if (!$eleve) {
            $this->flash('error', 'Élève introuvable.');
            Router::redirect('bulletins');
        }

        // Parent ne peut voir que son enfant
        if (AuthMiddleware::hasRole(ROLE_PARENT)) {
            $user = AuthMiddleware::user();
            if ($eleve['parent_id'] != $user['id']) {
                Router::redirect('bulletins');
            }
        }

        $moyennes  = $this->noteModel->moyennesParEleve($id, $periode);
        $moyGen    = $this->noteModel->moyenneGenerale($id, $periode);
        $rang      = $this->noteModel->rang($id, $eleve['classe_id'], $periode);

        // Nombre total d'élèves dans la classe
        $elevesClasse = $this->eleveModel->parClasse($eleve['classe_id']);
        $totalEleves  = count($elevesClasse);

        // Paramètres école depuis la BDD
        $params = $this->getParametres();

        // Mention selon la moyenne
        $mention = $this->getMention($moyGen);

        $layout = $print ? 'bulletin_print' : 'main';

        $this->render('bulletins/bulletin', [
            'title'       => 'Bulletin — ' . $eleve['prenom'] . ' ' . $eleve['nom'],
            'pageTitle'   => 'Bulletin de notes',
            'user'        => AuthMiddleware::user(),
            'flash'       => null,
            'eleve'       => $eleve,
            'moyennes'    => $moyennes,
            'moyGen'      => $moyGen,
            'rang'        => $rang,
            'totalEleves' => $totalEleves,
            'periode'     => $periode,
            'mention'     => $mention,
            'params'      => $params,
            'print'       => $print,
        ], $layout);
    }

    // ─────────────────────────────────────────
    // GET /bulletins/classe/{id}?periode=X
    // Liste des bulletins d'une classe
    // ─────────────────────────────────────────
    public function classe(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF]);

        $classeId = (int) $param;
        $periode  = (int) $this->get('periode', 1);
        $classe   = $this->classeModel->avecDetails($classeId);

        if (!$classe) {
            $this->flash('error', 'Classe introuvable.');
            Router::redirect('bulletins');
        }

        $eleves = $this->eleveModel->parClasse($classeId);

        // Calcul des moyennes pour chaque élève
        $resultats = [];
        foreach ($eleves as $eleve) {
            $moy = $this->noteModel->moyenneGenerale($eleve['id'], $periode);
            $resultats[] = [
                'eleve'   => $eleve,
                'moyenne' => $moy,
                'mention' => $this->getMention($moy),
            ];
        }

        // Trier par moyenne décroissante pour avoir les rangs
        usort($resultats, fn($a, $b) => $b['moyenne'] <=> $a['moyenne']);
        foreach ($resultats as $i => &$r) {
            $r['rang'] = $i + 1;
        }

        $this->render('bulletins/classe', [
            'title'      => 'Bulletins — ' . $classe['nom'],
            'pageTitle'  => 'Bulletins de la classe ' . $classe['nom'],
            'user'       => AuthMiddleware::user(),
            'flash'      => $this->getFlash(),
            'classe'     => $classe,
            'resultats'  => $resultats,
            'periode'    => $periode,
        ]);
    }

    // ─────────────────────────────────────────
    // GET /bulletins/lot/{classe_id}?periode=X
    // Impression en lot — tous les bulletins d'une classe
    // ─────────────────────────────────────────
    public function lot(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF]);

        $classeId = (int) $param;
        $periode  = (int) $this->get('periode', 1);
        $classe   = $this->classeModel->avecDetails($classeId);

        if (!$classe) {
            $this->flash('error', 'Classe introuvable.');
            Router::redirect('bulletins');
        }

        $eleves    = $this->eleveModel->parClasse($classeId);
        $params    = $this->getParametres();
        $nomEcole  = $params['nom_ecole'] ?? 'Groupe Scolaire';
        $adresse   = $params['adresse']   ?? 'Lomé, Togo';
        $telephone = $params['telephone'] ?? '';

        // Calculer les données pour chaque élève
        $bulletins = [];
        foreach ($eleves as $eleve) {
            $moyennes = $this->noteModel->moyennesParEleve($eleve['id'], $periode);
            $moyGen   = $this->noteModel->moyenneGenerale($eleve['id'], $periode);

            // Calculer le rang parmi la classe
            $rang = $this->noteModel->rang($eleve['id'], $classeId, $periode);

            $bulletins[] = [
                'eleve'       => $eleve,
                'moyennes'    => $moyennes,
                'moyGen'      => $moyGen,
                'rang'        => $rang,
                'totalEleves' => count($eleves),
                'mention'     => $this->getMention($moyGen),
            ];
        }

        // Trier par rang
        usort($bulletins, fn($a, $b) => $a['rang'] <=> $b['rang']);

        $this->render('bulletins/lot', [
            'title'      => 'Bulletins en lot — ' . $classe['nom'],
            'classe'     => $classe,
            'bulletins'  => $bulletins,
            'periode'    => $periode,
            'params'     => $params,
            'nomEcole'   => $nomEcole,
            'adresse'    => $adresse,
            'telephone'  => $telephone,
        ], 'lot_print');
    }

    // ─────────────────────────────────────────
    private function getMention(float $moy): string {
        if ($moy >= 16) return 'Très bien';
        if ($moy >= 14) return 'Bien';
        if ($moy >= 12) return 'Assez bien';
        if ($moy >= 10) return 'Passable';
        if ($moy > 0)   return 'Insuffisant';
        return '—';
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
