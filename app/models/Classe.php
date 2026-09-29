<?php
// app/models/Classe.php

require_once ROOT_PATH . '/app/core/Model.php';

class Classe extends Model {
    protected string $table = 'classes';

    public function classesActives(): array {
        return $this->query(
            "SELECT c.*, n.nom AS niveau_nom, n.cycle AS niveau_cycle,
                    a.libelle AS annee_libelle,
                    u.nom AS enseignant_nom, u.prenom AS enseignant_prenom,
                    COUNT(DISTINCT e.id) AS nb_eleves,
                    COUNT(DISTINCT cm.matiere_id) AS nb_matieres,
                    CASE
                        WHEN TRIM(c.niveau) = '' THEN TRIM(c.nom)
                        ELSE TRIM(CONCAT(COALESCE(NULLIF(n.nom, ''), c.niveau), ' ', c.nom))
                    END AS libelle_complete
             FROM `classes` c
             JOIN `annees_scolaires` a ON a.id = c.annee_scolaire_id AND a.active = 1
             LEFT JOIN `niveaux` n ON n.id = c.niveau_id
             LEFT JOIN `users` u ON u.id = c.enseignant_id
             LEFT JOIN `eleves` e ON e.classe_id = c.id AND e.actif = 1
             LEFT JOIN `classe_matieres` cm ON cm.classe_id = c.id AND cm.statut = 1
             WHERE c.statut = 1
             GROUP BY c.id, c.niveau_id, c.nom, c.niveau, c.annee_scolaire_id,
                      c.enseignant_id, c.effectif_maximum, c.statut,
                      c.created_at, c.updated_at, n.nom, n.cycle, a.libelle, u.nom, u.prenom
             ORDER BY n.ordre, c.nom"
        );
    }

    /**
     * Classes actives regroupées par cycle (collège / lycée).
     */
    public function classesParCycle(): array {
        $classes = $this->classesActives();
        $groupes = [
            'college' => ['label' => 'Collège (6ème → 3ème)', 'classes' => []],
            'lycee'   => ['label' => 'Lycée (2nde → Terminale)', 'classes' => []],
        ];

        foreach ($classes as $classe) {
            $cycle = $classe['niveau_cycle'] ?? 'college';
            if (!isset($groupes[$cycle])) {
                $groupes[$cycle] = ['label' => ucfirst($cycle), 'classes' => []];
            }
            $groupes[$cycle]['classes'][] = $classe;
        }

        return array_filter($groupes, fn($g) => !empty($g['classes']));
    }

    public function classesPourProf(int $profId): array {
        return $this->query(
            "SELECT DISTINCT c.*, n.nom AS niveau_nom, n.cycle AS niveau_cycle,
                    a.libelle AS annee_libelle,
                    CASE WHEN TRIM(c.niveau) = '' THEN TRIM(c.nom)
                         ELSE TRIM(CONCAT(COALESCE(NULLIF(n.nom, ''), c.niveau), ' ', c.nom))
                    END AS libelle_complete
             FROM teacher_assignments ta
             JOIN classes c ON c.id = ta.class_id AND c.annee_scolaire_id = ta.school_year_id
             JOIN niveaux n ON n.id = c.niveau_id
             JOIN annees_scolaires a ON a.id = c.annee_scolaire_id AND a.active = 1
             WHERE ta.teacher_id = ? AND ta.status = 1 AND c.statut = 1
             ORDER BY n.ordre, c.nom",
            [$profId]
        );
    }

    public function toutesLesClasses(): array {
        return $this->query(
            "SELECT c.*, n.nom AS niveau_nom, a.libelle AS annee,
                    CASE
                        WHEN TRIM(c.niveau) = '' THEN TRIM(c.nom)
                        ELSE TRIM(CONCAT(COALESCE(NULLIF(n.nom, ''), c.niveau), ' ', c.nom))
                    END AS libelle_complete
             FROM `classes` c
             JOIN `annees_scolaires` a ON a.id = c.annee_scolaire_id
             LEFT JOIN `niveaux` n ON n.id = c.niveau_id
             ORDER BY n.ordre, c.nom"
        );
    }

    public function avecDetails(int $id): ?array {
        return $this->queryOne(
            "SELECT c.*, n.nom AS niveau_nom, n.cycle AS niveau_cycle, a.libelle AS annee_libelle,
                    u.nom AS enseignant_nom, u.prenom AS enseignant_prenom,
                    COUNT(DISTINCT e.id) AS nb_eleves,
                    CASE
                        WHEN TRIM(c.niveau) = '' THEN TRIM(c.nom)
                        ELSE TRIM(CONCAT(COALESCE(NULLIF(n.nom, ''), c.niveau), ' ', c.nom))
                    END AS libelle_complete
             FROM `classes` c
             JOIN `annees_scolaires` a ON a.id = c.annee_scolaire_id
             LEFT JOIN `niveaux` n ON n.id = c.niveau_id
             LEFT JOIN `users` u ON u.id = c.enseignant_id
             LEFT JOIN `eleves` e ON e.classe_id = c.id AND e.actif = 1
             WHERE c.id = ?
             GROUP BY c.id, c.niveau_id, c.nom, c.niveau, c.annee_scolaire_id,
                      c.enseignant_id, c.effectif_maximum, c.statut,
                      c.created_at, c.updated_at, n.nom, n.cycle, a.libelle, u.nom, u.prenom
             LIMIT 1",
            [$id]
        );
    }

    public function tousLesNiveaux(): array {
        return $this->query(
            "SELECT * FROM `niveaux` WHERE statut = 1 ORDER BY `ordre` ASC, `nom` ASC"
        );
    }

    public function niveauxParCycle(): array {
        $niveaux = $this->tousLesNiveaux();
        $groupes = [
            'college' => ['label' => 'Collège', 'niveaux' => []],
            'lycee'   => ['label' => 'Lycée', 'niveaux' => []],
        ];
        foreach ($niveaux as $n) {
            $cycle = $n['cycle'] ?? 'college';
            $groupes[$cycle]['niveaux'][] = $n;
        }
        return array_filter($groupes, fn($g) => !empty($g['niveaux']));
    }

    public function findNiveauById(int $id): ?array {
        return $this->queryOne(
            "SELECT * FROM `niveaux` WHERE id = ? LIMIT 1",
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
