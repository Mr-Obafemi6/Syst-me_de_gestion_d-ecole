<?php
// app/models/Absence.php

require_once ROOT_PATH . '/app/core/Model.php';

class Absence extends Model {
    protected string $table = 'absences';

    /**
     * Absences paginées avec infos élève et classe
     */
    public function listerAvecDetails(int $page = 1, int $classeId = 0, int $eleveId = 0, string $justifiee = ''): array {
        $perPage = ITEMS_PER_PAGE;
        $offset  = ($page - 1) * $perPage;

        $where  = ['1=1'];
        $params = [];

        if ($classeId > 0) {
            $where[]  = "e.classe_id = ?";
            $params[] = $classeId;
        }
        if ($eleveId > 0) {
            $where[]  = "a.eleve_id = ?";
            $params[] = $eleveId;
        }
        if ($justifiee !== '') {
            $where[]  = "a.justifiee = ?";
            $params[] = (int) $justifiee;
        }

        $whereSQL = 'WHERE ' . implode(' AND ', $where);

        $total = (int) $this->queryScalar(
            "SELECT COUNT(*) FROM `absences` a
                JOIN `eleves` e ON e.id = a.eleve_id
                $whereSQL",
                $params
        );

        $params[] = $perPage;
        $params[] = $offset;

        $data = $this->query(
            "SELECT a.*,
                    e.nom AS eleve_nom, e.prenom AS eleve_prenom, e.matricule,
                    c.nom AS classe_nom,
                    u.nom AS saisie_par_nom, u.prenom AS saisie_par_prenom
                FROM `absences` a
                JOIN `eleves` e  ON e.id = a.eleve_id
                JOIN `classes` c ON c.id = e.classe_id
                JOIN `users`   u ON u.id = a.saisie_par
                $whereSQL
                ORDER BY a.date_absence DESC, a.id DESC
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
     * Absences d'un élève
     */
    public function parEleve(int $eleveId): array {
        return $this->query(
            "SELECT a.*, u.nom AS saisie_par_nom, u.prenom AS saisie_par_prenom
                FROM `absences` a
                JOIN `users` u ON u.id = a.saisie_par
                WHERE a.eleve_id = ?
                ORDER BY a.date_absence DESC",
            [$eleveId]
        );
    }

    /**
     * Statistiques des absences par élève
     */
    public function statsParEleve(int $eleveId): array {
        return $this->queryOne(
            "SELECT
                COUNT(*)                                          AS total,
                SUM(CASE WHEN justifiee = 1 THEN 1 ELSE 0 END)  AS justifiees,
                SUM(CASE WHEN justifiee = 0 THEN 1 ELSE 0 END)  AS non_justifiees
                FROM `absences`
                WHERE eleve_id = ?",
            [$eleveId]
        ) ?? ['total' => 0, 'justifiees' => 0, 'non_justifiees' => 0];
    }

    /**
     * Statistiques globales par classe
     */
    public function statsParClasse(int $classeId): array {
        return $this->query(
            "SELECT e.id, e.nom, e.prenom, e.matricule,
                    COUNT(a.id)                                           AS total_absences,
                    SUM(CASE WHEN a.justifiee = 1 THEN 1 ELSE 0 END)    AS justifiees,
                    SUM(CASE WHEN a.justifiee = 0 THEN 1 ELSE 0 END)    AS non_justifiees
                FROM `eleves` e
                LEFT JOIN `absences` a ON a.eleve_id = e.id
                WHERE e.classe_id = ? AND e.actif = 1
                GROUP BY e.id, e.nom, e.prenom, e.matricule
                ORDER BY total_absences DESC, e.nom",
            [$classeId]
        );
    }

    /**
     * Absences du jour pour toute l'école
     */
    public function absencesAujourdhui(): array {
        return $this->query(
            "SELECT a.*, e.nom AS eleve_nom, e.prenom AS eleve_prenom,
                    c.nom AS classe_nom
                FROM `absences` a
                JOIN `eleves` e  ON e.id = a.eleve_id
                JOIN `classes` c ON c.id = e.classe_id
                WHERE a.date_absence = CURDATE()
                ORDER BY c.nom, e.nom",
            []
        );
    }

    /**
     * Vérifie si une absence existe déjà pour un élève à une date
     */
    public function existeDeja(int $eleveId, string $date): bool {
        return (int) $this->queryScalar(
            "SELECT COUNT(*) FROM `absences` WHERE eleve_id = ? AND date_absence = ?",
            [$eleveId, $date]
        ) > 0;
    }
}
