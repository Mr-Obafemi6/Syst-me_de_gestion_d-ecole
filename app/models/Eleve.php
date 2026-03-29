<?php
// app/models/Eleve.php

require_once ROOT_PATH . '/app/core/Model.php';

class Eleve extends Model {
    protected string $table = 'eleves';

    /**
     * Liste paginée avec nom de classe
     */
    public function listerAvecClasse(int $page = 1, string $recherche = '', int $classeId = 0): array {
        $perPage = ITEMS_PER_PAGE;
        $offset  = ($page - 1) * $perPage;

        $where  = ['e.actif = 1'];
        $params = [];

        if ($recherche !== '') {
            $where[]  = "(e.nom LIKE ? OR e.prenom LIKE ? OR e.matricule LIKE ?)";
            $like     = '%' . $recherche . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        if ($classeId > 0) {
            $where[]  = "e.classe_id = ?";
            $params[] = $classeId;
        }

        $whereSQL = 'WHERE ' . implode(' AND ', $where);

        // Compter le total
        $total = (int) $this->queryScalar(
            "SELECT COUNT(*) FROM `eleves` e $whereSQL",
            $params
        );

        // Récupérer la page
        $params[] = $perPage;
        $params[] = $offset;

        $data = $this->query(
            "SELECT e.*, c.nom AS classe_nom, c.niveau
             FROM `eleves` e
             LEFT JOIN `classes` c ON c.id = e.classe_id
             $whereSQL
             ORDER BY e.nom, e.prenom
             LIMIT ? OFFSET ?",
            $params
        );

        return [
            'data'         => $data,
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int) ceil($total / $perPage),
            'recherche'    => $recherche,
            'classe_id'    => $classeId,
        ];
    }

    /**
     * Fiche complète d'un élève avec classe et parent
     */
    public function ficheComplete(int $id): ?array {
        return $this->queryOne(
            "SELECT e.*,
                    c.nom    AS classe_nom,
                    c.niveau AS classe_niveau,
                    u.nom    AS parent_nom,
                    u.prenom AS parent_prenom,
                    u.email  AS parent_email
             FROM `eleves` e
             LEFT JOIN `classes` c ON c.id = e.classe_id
             LEFT JOIN `users`   u ON u.id = e.parent_id
             WHERE e.id = ? AND e.actif = 1
             LIMIT 1",
            [$id]
        );
    }

    /**
     * Génère un matricule unique : SGE-YYYY-NNN
     */
    public function genererMatricule(): string {
        $annee = date('Y');
        $last  = $this->queryScalar(
            "SELECT MAX(CAST(SUBSTRING_INDEX(matricule, '-', -1) AS UNSIGNED))
             FROM `eleves`
             WHERE matricule LIKE ?",
            ["SGE-$annee-%"]
        );
        $num = ($last ?? 0) + 1;
        return sprintf('SGE-%s-%03d', $annee, $num);
    }

    /**
     * Vérifie si un matricule existe déjà
     */
    public function matriculeExiste(string $matricule, int $excludeId = 0): bool {
        return (int) $this->queryScalar(
            "SELECT COUNT(*) FROM `eleves` WHERE `matricule` = ? AND `id` != ?",
            [$matricule, $excludeId]
        ) > 0;
    }

    /**
     * Désactive un élève (soft delete)
     */
    public function desactiver(int $id): bool {
        return $this->update($id, ['actif' => 0]);
    }

    /**
     * Compte les élèves par classe
     */
    public function countParClasse(): array {
        return $this->query(
            "SELECT c.nom AS classe_nom, COUNT(e.id) AS total
             FROM `classes` c
             LEFT JOIN `eleves` e ON e.classe_id = c.id AND e.actif = 1
             GROUP BY c.id, c.nom
             ORDER BY c.nom"
        );
    }

    /**
     * Élèves d'une classe (pour saisie de notes)
     */
    public function parClasse(int $classeId): array {
        return $this->query(
            "SELECT id, matricule, nom, prenom
             FROM `eleves`
             WHERE classe_id = ? AND actif = 1
             ORDER BY nom, prenom",
            [$classeId]
        );
    }
}
