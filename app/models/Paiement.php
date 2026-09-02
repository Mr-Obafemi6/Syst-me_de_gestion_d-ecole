<?php
// app/models/Paiement.php

require_once ROOT_PATH . '/app/core/Model.php';

class Paiement extends Model {
    protected string $table = 'paiements';

    /**
     * Liste paginée avec infos élève et classe
     */
    public function listerAvecDetails(int $page = 1, int $eleveId = 0, string $statut = ''): array {
        $perPage = ITEMS_PER_PAGE;
        $offset  = ($page - 1) * $perPage;

        $where  = ['1=1'];
        $params = [];

        if ($eleveId > 0) {
            $where[]  = "p.eleve_id = ?";
            $params[] = $eleveId;
        }

        if ($statut !== '') {
            $where[]  = "p.statut = ?";
            $params[] = $statut;
        }

        $whereSQL = 'WHERE ' . implode(' AND ', $where);

        $total = (int) $this->queryScalar(
            "SELECT COUNT(*) FROM `paiements` p $whereSQL",
            $params
        );

        $params[] = $perPage;
        $params[] = $offset;

        $data = $this->query(
            "SELECT p.*,
                    e.nom AS eleve_nom, e.prenom AS eleve_prenom,
                    e.matricule, c.nom AS classe_nom,
                    u.nom AS createur_nom, u.prenom AS createur_prenom
             FROM `paiements` p
             JOIN `eleves` e  ON e.id = p.eleve_id
             JOIN `classes` c ON c.id = e.classe_id
             JOIN `users`   u ON u.id = p.created_by
             $whereSQL
             ORDER BY p.date_paiement DESC, p.id DESC
             LIMIT ? OFFSET ?",
            $params
        );

        return [
            'data'         => $data,
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int) ceil($total / $perPage),
        ];
    }

    /**
     * Paiements d'un élève
     */
    public function parEleve(int $eleveId): array {
        return $this->query(
            "SELECT p.*, u.nom AS createur_nom, u.prenom AS createur_prenom
             FROM `paiements` p
             JOIN `users` u ON u.id = p.created_by
             WHERE p.eleve_id = ?
             ORDER BY p.date_paiement DESC",
            [$eleveId]
        );
    }

    /**
     * Total payé par un élève pour une année
     */
    public function totalParEleve(int $eleveId, int $anneeId): float {
        return (float) $this->queryScalar(
            "SELECT COALESCE(SUM(montant_fcfa), 0)
             FROM `paiements`
             WHERE eleve_id = ? AND annee_id = ? AND statut != 'annule'",
            [$eleveId, $anneeId]
        );
    }

    /**
     * Génère un numéro de reçu unique
     */
    public function genererNumeroRecu(): string {
        $annee = date('Y');
        $last  = $this->queryScalar(
            "SELECT MAX(CAST(SUBSTRING_INDEX(recu_numero, '-', -1) AS UNSIGNED))
             FROM `paiements`
             WHERE recu_numero LIKE ?",
            ["SGE-PAY-$annee-%"]
        );
        $num = ($last ?? 0) + 1;
        return sprintf('SGE-PAY-%s-%04d', $annee, $num);
    }

    /**
     * Statistiques globales des paiements
     */
    public function statistiques(int $anneeId): array {
        return $this->queryOne(
            "SELECT
                COUNT(DISTINCT eleve_id)               AS nb_eleves_ayant_paye,
                COUNT(*)                               AS nb_paiements,
                COALESCE(SUM(montant_fcfa), 0)         AS total_encaisse,
                COALESCE(SUM(CASE WHEN statut='paye'    THEN montant_fcfa ELSE 0 END), 0) AS total_paye,
                COALESCE(SUM(CASE WHEN statut='partiel'  THEN montant_fcfa ELSE 0 END), 0) AS total_partiel,
                COALESCE(SUM(CASE WHEN statut='annule'   THEN montant_fcfa ELSE 0 END), 0) AS total_annule
             FROM `paiements`
             WHERE annee_id = ?",
            [$anneeId]
        ) ?? [];
    }

    /**
     * Encaissements par mois (pour graphique)
     */
    public function parMois(int $anneeId): array {
        return $this->query(
            "SELECT
                DATE_FORMAT(date_paiement, '%Y-%m') AS mois,
                DATE_FORMAT(date_paiement, '%b %Y') AS mois_label,
                SUM(montant_fcfa) AS total,
                COUNT(*) AS nb
             FROM `paiements`
             WHERE annee_id = ? AND statut != 'annule'
             GROUP BY DATE_FORMAT(date_paiement, '%Y-%m')
             ORDER BY mois",
            [$anneeId]
        );
    }

    /**
     * Paiement avec détails complets
     */
    public function avecDetails(int $id): ?array {
        return $this->queryOne(
            "SELECT p.*,
                    e.nom AS eleve_nom, e.prenom AS eleve_prenom,
                    e.matricule, e.date_naissance,
                    c.nom AS classe_nom, c.niveau,
                    a.libelle AS annee_libelle,
                    u.nom AS createur_nom, u.prenom AS createur_prenom
             FROM `paiements` p
             JOIN `eleves` e           ON e.id = p.eleve_id
             JOIN `classes` c          ON c.id = e.classe_id
             JOIN `annees_scolaires` a ON a.id = p.annee_id
             JOIN `users` u            ON u.id = p.created_by
             WHERE p.id = ?
             LIMIT 1",
            [$id]
        );
    }
}
