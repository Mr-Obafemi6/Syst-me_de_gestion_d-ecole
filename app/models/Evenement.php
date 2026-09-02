<?php
// app/models/Evenement.php

require_once ROOT_PATH . '/app/core/Model.php';

class Evenement extends Model {
    protected string $table = 'evenements';

    /**
     * Prochains événements (à partir de maintenant), avec le nom de la classe si renseignée.
     */
    public function prochains(int $limit = 4): array {
        return $this->query(
            "SELECT e.*, c.nom AS classe_nom
             FROM `evenements` e
             LEFT JOIN `classes` c ON c.id = e.classe_id
             WHERE e.date_debut >= NOW()
             ORDER BY e.date_debut ASC
             LIMIT ?",
            [$limit]
        );
    }

    /**
     * Liste complète triée par date, avec pagination simple.
     */
    public function listerAvecDetails(int $page = 1, int $perPage = 20): array {
        $offset = ($page - 1) * $perPage;
        $total  = $this->count();

        $data = $this->query(
            "SELECT e.*, c.nom AS classe_nom
             FROM `evenements` e
             LEFT JOIN `classes` c ON c.id = e.classe_id
             ORDER BY e.date_debut DESC
             LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );

        return [
            'data'         => $data,
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int) ceil($total / $perPage),
        ];
    }
}
