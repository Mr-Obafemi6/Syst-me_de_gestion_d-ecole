-- Migration : Table des événements (agenda du tableau de bord)
-- 2026-07-17

CREATE TABLE IF NOT EXISTS `evenements` (
    `id`          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `titre`       VARCHAR(150)    NOT NULL,
    `description` VARCHAR(500)    DEFAULT NULL,
    `date_debut`  DATETIME        NOT NULL,
    `type`        ENUM('reunion','sortie','examen','conseil','autre') NOT NULL DEFAULT 'autre',
    `classe_id`   INT UNSIGNED    DEFAULT NULL,
    `cree_par`    INT UNSIGNED    NOT NULL,
    `created_at`  TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_date_debut` (`date_debut`),
    CONSTRAINT `fk_evenements_classe`
        FOREIGN KEY (`classe_id`) REFERENCES `classes`(`id`)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_evenements_user`
        FOREIGN KEY (`cree_par`) REFERENCES `users`(`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 'Table evenements créée.' AS message;
