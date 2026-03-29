-- database/sge_db.sql
-- Système de Gestion d'École (SGE)
-- Compatible MySQL 5.7+ / MariaDB 10.3+

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `sge_db`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `sge_db`;

-- ===== ANNÉES SCOLAIRES =====
CREATE TABLE IF NOT EXISTS `annees_scolaires` (
    `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `libelle`    VARCHAR(20)     NOT NULL,
    `date_debut` DATE            NOT NULL,
    `date_fin`   DATE            NOT NULL,
    `active`     TINYINT(1)      NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_libelle` (`libelle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== UTILISATEURS =====
CREATE TABLE IF NOT EXISTS `users` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `nom`           VARCHAR(100)    NOT NULL,
    `prenom`        VARCHAR(100)    NOT NULL,
    `email`         VARCHAR(150)    NOT NULL,
    `password_hash` VARCHAR(255)    NOT NULL,
    `role`          ENUM('admin','professeur','parent') NOT NULL DEFAULT 'parent',
    `actif`         TINYINT(1)      NOT NULL DEFAULT 1,
    `reset_token`   VARCHAR(64)     DEFAULT NULL,
    `reset_expiry`  DATETIME        DEFAULT NULL,
    `created_at`    TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_email` (`email`),
    INDEX `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== CLASSES =====
CREATE TABLE IF NOT EXISTS `classes` (
    `id`                INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `nom`               VARCHAR(50)     NOT NULL,
    `niveau`            VARCHAR(50)     NOT NULL,
    `annee_scolaire_id` INT UNSIGNED    NOT NULL,
    `created_at`        TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_annee` (`annee_scolaire_id`),
    CONSTRAINT `fk_classes_annee`
        FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires`(`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== ÉLÈVES =====
CREATE TABLE IF NOT EXISTS `eleves` (
    `id`             INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `matricule`      VARCHAR(20)     NOT NULL,
    `nom`            VARCHAR(100)    NOT NULL,
    `prenom`         VARCHAR(100)    NOT NULL,
    `date_naissance` DATE            NOT NULL,
    `sexe`           ENUM('M','F')   NOT NULL,
    `classe_id`      INT UNSIGNED    NOT NULL,
    `parent_id`      INT UNSIGNED    DEFAULT NULL,
    `photo`          VARCHAR(255)    DEFAULT NULL,
    `actif`          TINYINT(1)      NOT NULL DEFAULT 1,
    `created_at`     TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     TIMESTAMP       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_matricule` (`matricule`),
    INDEX `idx_classe` (`classe_id`),
    INDEX `idx_parent` (`parent_id`),
    CONSTRAINT `fk_eleves_classe`
        FOREIGN KEY (`classe_id`) REFERENCES `classes`(`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_eleves_parent`
        FOREIGN KEY (`parent_id`) REFERENCES `users`(`id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== MATIÈRES =====
CREATE TABLE IF NOT EXISTS `matieres` (
    `id`          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `nom`         VARCHAR(100)    NOT NULL,
    `coefficient` DECIMAL(4,2)    NOT NULL DEFAULT 1.00,
    `classe_id`   INT UNSIGNED    NOT NULL,
    `prof_id`     INT UNSIGNED    DEFAULT NULL,
    `created_at`  TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_classe` (`classe_id`),
    INDEX `idx_prof`   (`prof_id`),
    CONSTRAINT `fk_matieres_classe`
        FOREIGN KEY (`classe_id`) REFERENCES `classes`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_matieres_prof`
        FOREIGN KEY (`prof_id`) REFERENCES `users`(`id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== NOTES =====
CREATE TABLE IF NOT EXISTS `notes` (
    `id`          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `eleve_id`    INT UNSIGNED    NOT NULL,
    `matiere_id`  INT UNSIGNED    NOT NULL,
    `note`        DECIMAL(5,2)    NOT NULL,
    `type_eval`   ENUM('devoir','composition','examen') NOT NULL DEFAULT 'devoir',
    `periode`     TINYINT         NOT NULL DEFAULT 1,
    `date_eval`   DATE            NOT NULL,
    `commentaire` VARCHAR(255)    DEFAULT NULL,
    `created_at`  TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_eleve`   (`eleve_id`),
    INDEX `idx_matiere` (`matiere_id`),
    CONSTRAINT `fk_notes_eleve`
        FOREIGN KEY (`eleve_id`) REFERENCES `eleves`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_notes_matiere`
        FOREIGN KEY (`matiere_id`) REFERENCES `matieres`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== PAIEMENTS =====
CREATE TABLE IF NOT EXISTS `paiements` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `eleve_id`      INT UNSIGNED    NOT NULL,
    `recu_numero`   VARCHAR(30)     NOT NULL,
    `montant_fcfa`  INT UNSIGNED    NOT NULL,
    `date_paiement` DATE            NOT NULL,
    `mode_paiement` ENUM('especes','mobile_money','virement') NOT NULL DEFAULT 'especes',
    `statut`        ENUM('paye','partiel','annule')            NOT NULL DEFAULT 'paye',
    `annee_id`      INT UNSIGNED    NOT NULL,
    `commentaire`   VARCHAR(255)    DEFAULT NULL,
    `created_by`    INT UNSIGNED    NOT NULL,
    `created_at`    TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_recu` (`recu_numero`),
    INDEX `idx_eleve` (`eleve_id`),
    INDEX `idx_annee` (`annee_id`),
    CONSTRAINT `fk_paiements_eleve`
        FOREIGN KEY (`eleve_id`) REFERENCES `eleves`(`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_paiements_annee`
        FOREIGN KEY (`annee_id`) REFERENCES `annees_scolaires`(`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_paiements_user`
        FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== PARAMÈTRES =====
CREATE TABLE IF NOT EXISTS `parametres` (
    `cle`    VARCHAR(50)  NOT NULL,
    `valeur` TEXT         DEFAULT NULL,
    PRIMARY KEY (`cle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== VUES =====
CREATE OR REPLACE VIEW `vue_moyennes_eleve` AS
SELECT
    n.eleve_id,
    n.matiere_id,
    n.periode,
    m.nom             AS matiere_nom,
    m.coefficient,
    ROUND(AVG(n.note), 2) AS moyenne,
    COUNT(n.id)       AS nb_notes
FROM `notes` n
JOIN `matieres` m ON m.id = n.matiere_id
GROUP BY n.eleve_id, n.matiere_id, n.periode, m.nom, m.coefficient;

CREATE OR REPLACE VIEW `vue_moyenne_generale` AS
SELECT
    v.eleve_id,
    v.periode,
    ROUND(
        SUM(v.moyenne * v.coefficient) / NULLIF(SUM(v.coefficient), 0)
    , 2) AS moyenne_generale
FROM `vue_moyennes_eleve` v
GROUP BY v.eleve_id, v.periode;

-- ===== DONNÉES DE BASE =====
INSERT INTO `annees_scolaires` (`libelle`, `date_debut`, `date_fin`, `active`)
VALUES ('2024-2025', '2024-10-01', '2025-07-31', 1);

-- Mot de passe : Admin1234!
INSERT INTO `users` (`nom`, `prenom`, `email`, `password_hash`, `role`) VALUES (
    'Admin', 'SGE', 'admin@sge.tg',
    '$2y$12$P/irYOz2KepqS4l07VKlJ.z6JV/Ykn15oR.5xy0rYdHRLpWxUjeZK',
    'admin'
);

INSERT INTO `parametres` (`cle`, `valeur`) VALUES
('nom_ecole',           'Groupe Scolaire de Lomé'),
('adresse',             'Lomé, Togo'),
('telephone',           '+228 00 00 00 00'),
('email',               'contact@ecole.tg'),
('logo',                ''),
('devise',              'FCFA'),
('frais_scol_primaire', '50000'),
('frais_scol_college',  '80000'),
('frais_scol_lycee',    '100000');

SET FOREIGN_KEY_CHECKS = 1;
SELECT 'OK - Base de données SGE créée avec succès.' AS message;

-- ===== OPTIMISATIONS PHASE 10 =====

-- Index supplémentaires pour les performances
ALTER TABLE `notes`
    ADD INDEX IF NOT EXISTS `idx_periode` (`periode`),
    ADD INDEX IF NOT EXISTS `idx_type_eval` (`type_eval`),
    ADD INDEX IF NOT EXISTS `idx_date_eval` (`date_eval`);

ALTER TABLE `paiements`
    ADD INDEX IF NOT EXISTS `idx_date_paiement` (`date_paiement`),
    ADD INDEX IF NOT EXISTS `idx_statut` (`statut`);

ALTER TABLE `eleves`
    ADD INDEX IF NOT EXISTS `idx_actif` (`actif`),
    ADD INDEX IF NOT EXISTS `idx_nom` (`nom`);

-- ===== DONNÉES DE TEST (commenter en production) =====
-- Professeur de test
INSERT IGNORE INTO `users` (`nom`, `prenom`, `email`, `password_hash`, `role`) VALUES (
    'MENSAH', 'Akossiwa',
    'prof@sge.tg',
    '$2y$12$P/irYOz2KepqS4l07VKlJ.z6JV/Ykn15oR.5xy0rYdHRLpWxUjeZK',
    'professeur'
);

-- Parent de test
INSERT IGNORE INTO `users` (`nom`, `prenom`, `email`, `password_hash`, `role`) VALUES (
    'KOFFI', 'Edem',
    'parent@sge.tg',
    '$2y$12$P/irYOz2KepqS4l07VKlJ.z6JV/Ykn15oR.5xy0rYdHRLpWxUjeZK',
    'parent'
);

-- Classe de test
INSERT IGNORE INTO `classes` (`nom`, `niveau`, `annee_scolaire_id`)
SELECT '6ème A', 'Sixième', id FROM `annees_scolaires` WHERE active = 1 LIMIT 1;

SELECT 'Optimisations Phase 10 appliquées.' AS message;
