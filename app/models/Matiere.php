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
    public function parClasse(int $classeId, bool $inclureInactives = false): array {
        $statutCondition = $inclureInactives ? '' : ' AND cm.statut = 1';
        return $this->query(
            "SELECT m.*, cm.coefficient, cm.volume_horaire, cm.statut AS classe_matiere_statut,
                    u.nom AS prof_nom, u.prenom AS prof_prenom
             FROM `classe_matieres` cm
             JOIN `classes` c ON c.id = cm.classe_id AND c.annee_scolaire_id = cm.annee_scolaire_id
             JOIN `matieres` m ON m.id = cm.matiere_id
             LEFT JOIN `users` u ON u.id = m.prof_id
             WHERE cm.classe_id = ?{$statutCondition} AND m.statut = 1
             ORDER BY m.nom",
            [$classeId]
        );
    }

    public function parClassePourProf(int $classeId, int $profId): array {
        return $this->query(
            "SELECT m.*, cm.coefficient, cm.volume_horaire, cm.statut AS classe_matiere_statut,
                    u.nom AS prof_nom, u.prenom AS prof_prenom
             FROM `teacher_assignments` ta
             JOIN `classe_matieres` cm ON cm.classe_id = ta.class_id
                    AND cm.matiere_id = ta.subject_id
                    AND cm.annee_scolaire_id = ta.school_year_id
                    AND cm.statut = 1
             JOIN `matieres` m ON m.id = cm.matiere_id AND m.statut = 1
             LEFT JOIN `users` u ON u.id = m.prof_id
             WHERE ta.teacher_id = ? AND ta.class_id = ? AND ta.status = 1
             ORDER BY m.nom",
            [$profId, $classeId]
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
                    CASE
                        WHEN TRIM(c.niveau) = '' THEN TRIM(c.nom)
                        ELSE TRIM(CONCAT(COALESCE(NULLIF(n.nom, ''), c.niveau), ' ', c.nom))
                    END AS libelle_complete,
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
            $row['matieres'] = $this->parClasse((int) $row['classe_id'], true);
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
    public function assignerAClasse(int $matiereId, int $classeId, ?float $coefficient, ?float $volumeHoraire = null, ?int $anneeScolaireId = null, int $statut = 1): void {
        if ($anneeScolaireId === null) {
            $anneeScolaireId = (int) $this->queryScalar(
                "SELECT annee_scolaire_id FROM `classes` WHERE id = ? LIMIT 1",
                [$classeId]
            );
        }

        $this->db->prepare(
            "INSERT INTO `classe_matieres`
                    (`classe_id`, `matiere_id`, `coefficient`, `volume_horaire`, `annee_scolaire_id`, `statut`)
             VALUES (?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                    `coefficient` = VALUES(`coefficient`),
                    `volume_horaire` = VALUES(`volume_horaire`),
                    `statut` = VALUES(`statut`)"
        )->execute([$classeId, $matiereId, $coefficient, $volumeHoraire, $anneeScolaireId, $statut]);
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
