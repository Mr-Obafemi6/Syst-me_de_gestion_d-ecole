<?php
// app/models/Classe.php

require_once ROOT_PATH . '/app/core/Model.php';

class Classe extends Model {
    protected string $table = 'classes';

    public function classesActives(): array {
        return $this->query(
            "SELECT c.*, a.libelle AS annee_libelle,
                    COUNT(DISTINCT e.id) AS nb_eleves,
                    COUNT(DISTINCT m.id) AS nb_matieres
             FROM `classes` c
             JOIN `annees_scolaires` a ON a.id = c.annee_scolaire_id AND a.active = 1
             LEFT JOIN `eleves` e ON e.classe_id = c.id AND e.actif = 1
             LEFT JOIN `matieres` m ON m.classe_id = c.id
             GROUP BY c.id, c.nom, c.niveau, c.annee_scolaire_id, c.created_at, a.libelle
             ORDER BY c.niveau, c.nom"
        );
    }

    public function toutesLesClasses(): array {
        return $this->query(
            "SELECT c.id, c.nom, c.niveau, a.libelle AS annee
             FROM `classes` c
             JOIN `annees_scolaires` a ON a.id = c.annee_scolaire_id
             ORDER BY c.niveau, c.nom"
        );
    }

    public function avecDetails(int $id): ?array {
        return $this->queryOne(
            "SELECT c.*, a.libelle AS annee_libelle,
                    COUNT(DISTINCT e.id) AS nb_eleves
             FROM `classes` c
             JOIN `annees_scolaires` a ON a.id = c.annee_scolaire_id
             LEFT JOIN `eleves` e ON e.classe_id = c.id AND e.actif = 1
             WHERE c.id = ?
             GROUP BY c.id, c.nom, c.niveau, c.annee_scolaire_id, c.created_at, a.libelle
             LIMIT 1",
            [$id]
        );
    }

    public function anneeActive(): ?array {
        return $this->queryOne(
            "SELECT * FROM `annees_scolaires` WHERE active = 1 LIMIT 1"
        );
    }

    public function toutesLesAnnees(): array {
        return $this->query(
            "SELECT * FROM `annees_scolaires` ORDER BY libelle DESC"
        );
    }

    public function tousLesProfesseurs(): array {
        return $this->query(
            "SELECT id, nom, prenom, email
             FROM `users`
             WHERE role = 'professeur' AND actif = 1
             ORDER BY nom, prenom"
        );
    }
}
