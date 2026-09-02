<?php
// app/models/Matiere.php

require_once ROOT_PATH . '/app/core/Model.php';

class Matiere extends Model {
    protected string $table = 'matieres';

    /**
     * Toutes les matières, configurables pour l'administration.
     */
    public function toutesAvecDetails(): array {
        return $this->query(
            "SELECT m.*, COUNT(DISTINCT cm.classe_id) AS nb_classes,
                    COALESCE(SUM(CASE WHEN cm.statut = 1 THEN cm.coefficient ELSE 0 END), m.coefficient) AS coefficient_global,
                    u.nom AS prof_nom, u.prenom AS prof_prenom
             FROM `matieres` m
             LEFT JOIN `classe_matieres` cm ON cm.matiere_id = m.id AND cm.statut = 1
             LEFT JOIN `users` u ON u.id = m.prof_id
             GROUP BY m.id, m.nom, m.code, m.description, m.coefficient, m.prof_id, m.statut, m.created_at, m.updated_at, u.nom, u.prenom
             ORDER BY m.nom"
        );
    }

    /**
     * Matières d'une classe, prioritairement via table d'association.
     */
    public function parClasse(int $classeId): array {
        return $this->query(
            "SELECT m.*, cm.coefficient,
                    u.nom AS prof_nom, u.prenom AS prof_prenom
             FROM `classe_matieres` cm
             JOIN `matieres` m ON m.id = cm.matiere_id
             LEFT JOIN `users` u ON u.id = m.prof_id
             WHERE cm.classe_id = ? AND cm.statut = 1 AND m.statut = 1
             ORDER BY m.nom",
            [$classeId]
        );
    }

    /**
     * Matières assignées à un professeur
     */
    public function parProf(int $profId): array {
        return $this->query(
            "SELECT DISTINCT m.*, c.nom AS classe_nom, c.niveau,
                    COALESCE(cm.coefficient, m.coefficient) AS coefficient
             FROM `matieres` m
             LEFT JOIN `classe_matieres` cm ON cm.matiere_id = m.id
             LEFT JOIN `classes` c ON c.id = cm.classe_id
             WHERE m.prof_id = ? AND (cm.statut = 1 OR m.statut = 1)
             ORDER BY c.nom, m.nom",
            [$profId]
        );
    }

    /**
     * Matière avec détails complets
     */
    public function avecDetails(int $id): ?array {
        return $this->queryOne(
            "SELECT m.*, u.nom AS prof_nom, u.prenom AS prof_prenom,
                    COUNT(DISTINCT cm.classe_id) AS nb_classes
             FROM `matieres` m
             LEFT JOIN `users` u ON u.id = m.prof_id
             LEFT JOIN `classe_matieres` cm ON cm.matiere_id = m.id AND cm.statut = 1
             WHERE m.id = ?
             GROUP BY m.id, m.nom, m.code, m.description, m.coefficient, m.prof_id,
                      m.statut, m.created_at, m.updated_at, u.nom, u.prenom
             LIMIT 1",
            [$id]
        );
    }

    /**
     * Toutes les classes actives avec leurs matières assignées.
     */
    public function classesAvecMatieres(): array {
        $rows = $this->query(
            "SELECT c.id AS classe_id, c.nom AS classe_nom, c.niveau,
                    n.nom AS niveau_nom, n.cycle AS niveau_cycle, n.ordre AS niveau_ordre,
                    TRIM(CONCAT(COALESCE(n.nom, c.niveau), ' ', c.nom)) AS libelle_complete,
                    COUNT(DISTINCT e.id) AS nb_eleves
             FROM `classes` c
             JOIN `annees_scolaires` a ON a.id = c.annee_scolaire_id AND a.active = 1
             LEFT JOIN `niveaux` n ON n.id = c.niveau_id
             LEFT JOIN `eleves` e ON e.classe_id = c.id AND e.actif = 1
             WHERE c.statut = 1
             GROUP BY c.id, c.nom, c.niveau, n.nom, n.cycle, n.ordre
             ORDER BY n.ordre, c.nom"
        );

        foreach ($rows as &$row) {
            $row['matieres'] = $this->parClasse((int) $row['classe_id']);
        }
        unset($row);

        return $rows;
    }

    /**
     * Catalogue des matières disponibles (non encore assignées à une classe).
     */
    public function catalogue(): array {
        return $this->query(
            "SELECT * FROM `matieres` WHERE statut = 1 ORDER BY nom"
        );
    }

    /**
     * Assigne une matière existante à une classe.
     */
    public function assignerAClasse(int $matiereId, int $classeId, float $coefficient): void {
        $this->db->prepare(
            "INSERT INTO `classe_matieres` (`classe_id`, `matiere_id`, `coefficient`, `statut`)
             VALUES (?, ?, ?, 1)
             ON DUPLICATE KEY UPDATE `coefficient` = VALUES(`coefficient`), `statut` = 1"
        )->execute([$classeId, $matiereId, $coefficient]);
    }

    /**
     * Retire une matière d'une classe (sans supprimer du catalogue).
     */
    public function retirerDeClasse(int $matiereId, int $classeId): void {
        $this->db->prepare(
            "DELETE FROM `classe_matieres` WHERE `classe_id` = ? AND `matiere_id` = ?"
        )->execute([$classeId, $matiereId]);
    }

    /**
     * Total des coefficients d'une classe
     */
    public function totalCoefficients(int $classeId): float {
        return (float) $this->queryScalar(
            "SELECT COALESCE(SUM(cm.coefficient), 0)
             FROM `classe_matieres` cm
             JOIN `matieres` m ON m.id = cm.matiere_id
             WHERE cm.classe_id = ? AND cm.statut = 1 AND m.statut = 1",
            [$classeId]
        );
    }
}
