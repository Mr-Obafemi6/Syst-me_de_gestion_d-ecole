<?php
// app/models/Matiere.php

require_once ROOT_PATH . '/app/core/Model.php';

class Matiere extends Model {
    protected string $table = 'matieres';

    /**
     * Toutes les matières avec nom de classe et prof
     */
    public function toutesAvecDetails(): array {
        return $this->query(
            "SELECT m.*, c.nom AS classe_nom, c.niveau,
                    u.nom AS prof_nom, u.prenom AS prof_prenom
             FROM `matieres` m
             JOIN `classes` c ON c.id = m.classe_id
             LEFT JOIN `users` u ON u.id = m.prof_id
             ORDER BY c.nom, m.nom"
        );
    }

    /**
     * Matières d'une classe
     */
    public function parClasse(int $classeId): array {
        return $this->query(
            "SELECT m.*, u.nom AS prof_nom, u.prenom AS prof_prenom
             FROM `matieres` m
             LEFT JOIN `users` u ON u.id = m.prof_id
             WHERE m.classe_id = ?
             ORDER BY m.nom",
            [$classeId]
        );
    }

    /**
     * Matières assignées à un professeur
     */
    public function parProf(int $profId): array {
        return $this->query(
            "SELECT m.*, c.nom AS classe_nom, c.niveau
             FROM `matieres` m
             JOIN `classes` c ON c.id = m.classe_id
             WHERE m.prof_id = ?
             ORDER BY c.nom, m.nom",
            [$profId]
        );
    }

    /**
     * Matière avec détails complets
     */
    public function avecDetails(int $id): ?array {
        return $this->queryOne(
            "SELECT m.*, c.nom AS classe_nom, c.niveau,
                    u.nom AS prof_nom, u.prenom AS prof_prenom
             FROM `matieres` m
             JOIN `classes` c ON c.id = m.classe_id
             LEFT JOIN `users` u ON u.id = m.prof_id
             WHERE m.id = ? LIMIT 1",
            [$id]
        );
    }

    /**
     * Total des coefficients d'une classe
     */
    public function totalCoefficients(int $classeId): float {
        return (float) $this->queryScalar(
            "SELECT SUM(coefficient) FROM `matieres` WHERE classe_id = ?",
            [$classeId]
        );
    }
}
