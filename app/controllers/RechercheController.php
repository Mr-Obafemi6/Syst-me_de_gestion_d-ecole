<?php
// app/controllers/RechercheController.php

require_once ROOT_PATH . '/app/core/Controller.php';

class RechercheController extends Controller {

    public function index(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF]);

        $q = trim($this->get('q', ''));

        if (empty($q) || strlen($q) < 2) {
            $this->render('recherche/index', [
                'title'     => 'Recherche',
                'pageTitle' => 'Recherche globale',
                'user'      => AuthMiddleware::user(),
                'flash'     => null,
                'q'         => $q,
                'resultats' => null,
            ]);
            return;
        }

        $db   = Database::getConnection();
        $like = '%' . $q . '%';

        // Recherche élèves
        $eleves = $db->prepare(
            "SELECT e.id, e.matricule, e.nom, e.prenom, e.date_naissance, c.nom AS classe_nom
             FROM `eleves` e
             LEFT JOIN `classes` c ON c.id = e.classe_id
             WHERE e.actif = 1
               AND (e.nom LIKE ? OR e.prenom LIKE ? OR e.matricule LIKE ?)
             ORDER BY e.nom, e.prenom
             LIMIT 20"
        );
        $eleves->execute([$like, $like, $like]);
        $resultats['eleves'] = $eleves->fetchAll();

        // Recherche utilisateurs
        $users = $db->prepare(
            "SELECT id, nom, prenom, email, role
             FROM `users`
             WHERE actif = 1
               AND (nom LIKE ? OR prenom LIKE ? OR email LIKE ?)
             ORDER BY nom, prenom
             LIMIT 10"
        );
        $users->execute([$like, $like, $like]);
        $resultats['users'] = $users->fetchAll();

        // Recherche classes
        $classes = $db->prepare(
            "SELECT c.id, c.nom, c.niveau, a.libelle AS annee,
                    COUNT(e.id) AS nb_eleves
             FROM `classes` c
             JOIN `annees_scolaires` a ON a.id = c.annee_scolaire_id
             LEFT JOIN `eleves` e ON e.classe_id = c.id AND e.actif = 1
             WHERE c.nom LIKE ? OR c.niveau LIKE ?
             GROUP BY c.id, c.nom, c.niveau, a.libelle
             ORDER BY c.nom
             LIMIT 10"
        );
        $classes->execute([$like, $like]);
        $resultats['classes'] = $classes->fetchAll();

        // Recherche paiements (par numéro de reçu)
        $paiements = $db->prepare(
            "SELECT p.id, p.recu_numero, p.montant_fcfa, p.date_paiement, p.statut,
                    e.nom AS eleve_nom, e.prenom AS eleve_prenom
             FROM `paiements` p
             JOIN `eleves` e ON e.id = p.eleve_id
             WHERE p.recu_numero LIKE ? OR e.nom LIKE ? OR e.prenom LIKE ?
             ORDER BY p.date_paiement DESC
             LIMIT 10"
        );
        $paiements->execute([$like, $like, $like]);
        $resultats['paiements'] = $paiements->fetchAll();

        // Total résultats
        $resultats['total'] =
            count($resultats['eleves']) +
            count($resultats['users']) +
            count($resultats['classes']) +
            count($resultats['paiements']);

        $this->render('recherche/index', [
            'title'     => 'Recherche : ' . $q,
            'pageTitle' => 'Résultats de recherche',
            'user'      => AuthMiddleware::user(),
            'flash'     => null,
            'q'         => $q,
            'resultats' => $resultats,
        ]);
    }

    /**
     * API JSON pour la recherche en temps réel (topbar)
     */
    public function api(?string $param = null): void {
        AuthMiddleware::requireRole([ROLE_ADMIN, ROLE_PROF]);

        $q = trim($this->get('q', ''));

        if (strlen($q) < 2) {
            $this->json(['results' => []]);
        }

        $db   = Database::getConnection();
        $like = '%' . $q . '%';

        $results = [];

        // Élèves
        $stmt = $db->prepare(
            "SELECT e.id, e.matricule, e.nom, e.prenom, c.nom AS classe_nom
             FROM `eleves` e
             LEFT JOIN `classes` c ON c.id = e.classe_id
             WHERE e.actif = 1
               AND (e.nom LIKE ? OR e.prenom LIKE ? OR e.matricule LIKE ?)
             LIMIT 5"
        );
        $stmt->execute([$like, $like, $like]);
        foreach ($stmt->fetchAll() as $el) {
            $results[] = [
                'type'  => 'eleve',
                'icon'  => 'person-fill',
                'label' => $el['prenom'] . ' ' . $el['nom'],
                'sub'   => $el['matricule'] . ' — ' . ($el['classe_nom'] ?? ''),
                'url'   => BASE_URL . '/eleves/fiche/' . $el['id'],
            ];
        }

        // Classes
        $stmt = $db->prepare(
            "SELECT id, nom, niveau FROM `classes`
             WHERE nom LIKE ? OR niveau LIKE ? LIMIT 3"
        );
        $stmt->execute([$like, $like]);
        foreach ($stmt->fetchAll() as $cl) {
            $results[] = [
                'type'  => 'classe',
                'icon'  => 'building',
                'label' => $cl['nom'],
                'sub'   => $cl['niveau'],
                'url'   => BASE_URL . '/classes/detail/' . $cl['id'],
            ];
        }

        // Reçus paiements
        $stmt = $db->prepare(
            "SELECT p.id, p.recu_numero, e.nom, e.prenom
             FROM `paiements` p
             JOIN `eleves` e ON e.id = p.eleve_id
             WHERE p.recu_numero LIKE ? LIMIT 3"
        );
        $stmt->execute([$like]);
        foreach ($stmt->fetchAll() as $pa) {
            $results[] = [
                'type'  => 'paiement',
                'icon'  => 'receipt',
                'label' => $pa['recu_numero'],
                'sub'   => $pa['prenom'] . ' ' . $pa['nom'],
                'url'   => BASE_URL . '/paiements/recu/' . $pa['id'],
            ];
        }

        $this->json(['results' => $results, 'q' => $q]);
    }
}
