<?php
// app/models/Note.php

require_once ROOT_PATH . '/app/core/Model.php';

class Note extends Model {
    protected string $table = 'notes';

    /**
     * Notes d'un élève avec détails matière
     */
    public function parEleve(int $eleveId, int $periode = 0): array {
        $where  = "n.eleve_id = ?";
        $params = [$eleveId];

        if ($periode > 0) {
            $where   .= " AND n.periode = ?";
            $params[] = $periode;
        }

        return $this->query(
            "SELECT n.*, m.nom AS matiere_nom, m.coefficient,
                    c.nom AS classe_nom
             FROM `notes` n
             JOIN `matieres` m ON m.id = n.matiere_id
             JOIN `classes`  c ON c.id = m.classe_id
             WHERE $where
             ORDER BY n.periode, m.nom, n.date_eval",
            $params
        );
    }

    /**
     * Notes d'une matière pour tous les élèves d'une classe
     */
    public function parMatiereEtClasse(int $matiereId, int $periode = 1): array {
        return $this->query(
            "SELECT n.*, e.nom AS eleve_nom, e.prenom AS eleve_prenom, e.matricule
             FROM `notes` n
             JOIN `eleves` e ON e.id = n.eleve_id
             WHERE n.matiere_id = ? AND n.periode = ? AND e.actif = 1
             ORDER BY e.nom, e.prenom, n.date_eval",
            [$matiereId, $periode]
        );
    }

    /**
     * Moyennes d'un élève par matière et période
     */
    public function moyennesParEleve(int $eleveId, int $periode = 0): array {
        $where  = "n.eleve_id = ?";
        $params = [$eleveId];

        if ($periode > 0) {
            $where   .= " AND n.periode = ?";
            $params[] = $periode;
        }

        return $this->query(
            "SELECT m.id AS matiere_id, m.nom AS matiere_nom, m.coefficient,
                    n.periode,
                    ROUND(AVG(n.note), 2) AS moyenne,
                    MIN(n.note) AS note_min,
                    MAX(n.note) AS note_max,
                    COUNT(n.id) AS nb_notes
             FROM `notes` n
             JOIN `matieres` m ON m.id = n.matiere_id
             WHERE $where
             GROUP BY m.id, m.nom, m.coefficient, n.periode
             ORDER BY n.periode, m.nom",
            $params
        );
    }

    /**
     * Moyenne générale pondérée d'un élève pour une période
     */
    public function moyenneGenerale(int $eleveId, int $periode): float {
        $result = $this->queryOne(
            "SELECT
                ROUND(
                    SUM(sub.moyenne * sub.coefficient) / NULLIF(SUM(sub.coefficient), 0)
                , 2) AS moy_gen
             FROM (
                SELECT m.coefficient,
                       ROUND(AVG(n.note), 2) AS moyenne
                FROM `notes` n
                JOIN `matieres` m ON m.id = n.matiere_id
                WHERE n.eleve_id = ? AND n.periode = ?
                GROUP BY m.id, m.coefficient
             ) sub",
            [$eleveId, $periode]
        );
        return (float) ($result['moy_gen'] ?? 0);
    }

    /**
     * Rang d'un élève dans sa classe pour une période
     */
    public function rang(int $eleveId, int $classeId, int $periode): int {
        $result = $this->queryOne(
            "SELECT COUNT(*) + 1 AS rang
             FROM (
                SELECT e2.id,
                    ROUND(SUM(sub.moyenne * sub.coefficient) / NULLIF(SUM(sub.coefficient),0), 2) AS moy
                FROM `eleves` e2
                JOIN (
                    SELECT n.eleve_id, m.coefficient,
                           ROUND(AVG(n.note),2) AS moyenne
                    FROM `notes` n
                    JOIN `matieres` m ON m.id = n.matiere_id
                    WHERE n.periode = ?
                    GROUP BY n.eleve_id, m.id, m.coefficient
                ) sub ON sub.eleve_id = e2.id
                WHERE e2.classe_id = ? AND e2.actif = 1
                GROUP BY e2.id
             ) classement
             WHERE classement.moy > (
                SELECT ROUND(SUM(s2.moyenne * s2.coefficient) / NULLIF(SUM(s2.coefficient),0), 2)
                FROM (
                    SELECT m2.coefficient, ROUND(AVG(n2.note),2) AS moyenne
                    FROM `notes` n2
                    JOIN `matieres` m2 ON m2.id = n2.matiere_id
                    WHERE n2.eleve_id = ? AND n2.periode = ?
                    GROUP BY m2.id, m2.coefficient
                ) s2
             )",
            [$periode, $classeId, $eleveId, $periode]
        );
        return (int) ($result['rang'] ?? 1);
    }

    /**
     * Supprimer les notes d'un élève pour une matière/période
     */
    public function supprimerParMatiereEtEleve(int $eleveId, int $matiereId, int $periode): bool {
        $stmt = $this->db->prepare(
            "DELETE FROM `notes` WHERE eleve_id = ? AND matiere_id = ? AND periode = ?"
        );
        return $stmt->execute([$eleveId, $matiereId, $periode]);
    }
}
