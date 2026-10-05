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

-- ===== NIVEAUX (collège & lycée) =====
CREATE TABLE IF NOT EXISTS `niveaux` (
    `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `nom`        VARCHAR(30)     NOT NULL,
    `ordre`      TINYINT UNSIGNED NOT NULL,
    `cycle`      ENUM('college','lycee') NOT NULL DEFAULT 'college',
    `statut`     TINYINT(1)      NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_niveau_nom` (`nom`),
    UNIQUE KEY `uk_niveau_ordre` (`ordre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== CLASSES =====
CREATE TABLE IF NOT EXISTS `classes` (
    `id`                INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `niveau_id`         INT UNSIGNED    NOT NULL,
    `nom`               VARCHAR(50)     NOT NULL,
    `niveau`            VARCHAR(50)     NOT NULL DEFAULT '',
    `annee_scolaire_id` INT UNSIGNED    NOT NULL,
    `enseignant_id`     INT UNSIGNED    DEFAULT NULL,
    `effectif_maximum`  INT UNSIGNED    NOT NULL DEFAULT 30,
    `statut`            TINYINT(1)      NOT NULL DEFAULT 1,
    `created_at`        TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        TIMESTAMP       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_niveau` (`niveau_id`),
    INDEX `idx_annee` (`annee_scolaire_id`),
    INDEX `idx_enseignant` (`enseignant_id`),
    UNIQUE KEY `uk_classe_annee_nom` (`annee_scolaire_id`, `nom`),
    CONSTRAINT `fk_classes_niveau`
        FOREIGN KEY (`niveau_id`) REFERENCES `niveaux`(`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_classes_annee`
        FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires`(`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_classes_enseignant`
        FOREIGN KEY (`enseignant_id`) REFERENCES `users`(`id`)
        ON DELETE SET NULL ON UPDATE CASCADE
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
    `code`        VARCHAR(20)     DEFAULT NULL,
    `description` TEXT            DEFAULT NULL,
    `coefficient` DECIMAL(4,2)    NOT NULL DEFAULT 1.00,
    `classe_id`   INT UNSIGNED    DEFAULT NULL,
    `prof_id`     INT UNSIGNED    DEFAULT NULL,
    `statut`      TINYINT(1)      NOT NULL DEFAULT 1,
    `created_at`  TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_matiere_nom` (`nom`),
    INDEX `idx_classe` (`classe_id`),
    INDEX `idx_prof`   (`prof_id`),
    CONSTRAINT `fk_matieres_classe`
        FOREIGN KEY (`classe_id`) REFERENCES `classes`(`id`)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_matieres_prof`
        FOREIGN KEY (`prof_id`) REFERENCES `users`(`id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== ASSOCIATION CLASSE / MATIÈRES =====
CREATE TABLE IF NOT EXISTS `classe_matieres` (
    `id`          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `classe_id`   INT UNSIGNED    NOT NULL,
    `matiere_id`  INT UNSIGNED    NOT NULL,
    `coefficient` DECIMAL(4,2)    DEFAULT NULL,
    `volume_horaire` DECIMAL(5,2) DEFAULT NULL,
    `annee_scolaire_id` INT UNSIGNED NOT NULL,
    `statut`      TINYINT(1)      NOT NULL DEFAULT 1,
    `created_at`  TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_classe_matiere_annee` (`classe_id`, `matiere_id`, `annee_scolaire_id`),
    INDEX `idx_classe` (`classe_id`),
    INDEX `idx_matiere` (`matiere_id`),
    CONSTRAINT `fk_classe_matieres_classe`
        FOREIGN KEY (`classe_id`) REFERENCES `classes`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_classe_matieres_matiere`
        FOREIGN KEY (`matiere_id`) REFERENCES `matieres`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
    ,CONSTRAINT `fk_classe_matieres_annee`
        FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires`(`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== PÉRIODES D'ÉVALUATION =====
CREATE TABLE IF NOT EXISTS `periodes` (
    `id`               INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `nom`              VARCHAR(100)    NOT NULL,
    `ordre`            TINYINT UNSIGNED NOT NULL,
    `annee_scolaire_id` INT UNSIGNED    NOT NULL,
    `date_debut`       DATE            DEFAULT NULL,
    `date_fin`         DATE            DEFAULT NULL,
    `statut`           TINYINT(1)      NOT NULL DEFAULT 1,
    `created_at`       TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       TIMESTAMP       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_periode_annee_nom` (`annee_scolaire_id`, `nom`),
    INDEX `idx_annee` (`annee_scolaire_id`),
    CONSTRAINT `fk_periodes_annee`
        FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
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
    `mode_paiement` ENUM('especes','mobile_money','virement','flooz','tymoni') NOT NULL DEFAULT 'especes',
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
VALUES ('2024-2025', '2024-10-01', '2025-07-31', 1)
ON DUPLICATE KEY UPDATE `date_debut` = VALUES(`date_debut`), `date_fin` = VALUES(`date_fin`);

INSERT INTO `niveaux` (`nom`, `ordre`, `cycle`, `statut`)
VALUES
('6ème',          1,  'college', 1),
('5ème',          2,  'college', 1),
('4ème',          3,  'college', 1),
('3ème',          4,  'college', 1),
('2nde S/D',      5,  'lycee',   1),
('2nde A4',       6,  'lycee',   1),
('1ère D',        7,  'lycee',   1),
('1ère A4',       8,  'lycee',   1),
('Terminale D',   9,  'lycee',   1),
('Terminale A4', 10,  'lycee',   1)
ON DUPLICATE KEY UPDATE `ordre` = VALUES(`ordre`), `cycle` = VALUES(`cycle`), `statut` = VALUES(`statut`);

INSERT INTO `matieres` (`nom`, `code`, `description`, `coefficient`, `statut`)
VALUES
('Français', 'FR', 'Français', 3.00, 1),
('Mathématiques', 'MATH', 'Mathématiques', 4.00, 1),
('Physique-Chimie', 'PC', 'Physique-Chimie', 3.00, 1),
('SVT', 'SVT', 'Sciences de la Vie et de la Terre', 2.00, 1),
('Histoire-Géographie', 'HG', 'Histoire-Géographie', 2.00, 1),
('Anglais', 'ANG', 'Anglais', 2.00, 1),
('Philosophie', 'PHIL', 'Philosophie', 2.00, 1),
('EPS', 'EPS', 'Éducation Physique et Sportive', 1.00, 1),
('Espagnol', 'ESP', 'Espagnol', 2.00, 1),
('Allemand', 'ALL', 'Allemand', 2.00, 1),
('Informatique', 'INFO', 'Informatique', 1.00, 1),
('Éducation civique', 'EC', 'Éducation civique', 1.00, 1)
ON DUPLICATE KEY UPDATE `code` = VALUES(`code`), `description` = VALUES(`description`), `coefficient` = VALUES(`coefficient`), `statut` = VALUES(`statut`);

INSERT INTO `periodes` (`nom`, `ordre`, `annee_scolaire_id`, `date_debut`, `date_fin`, `statut`)
SELECT 'Premier trimestre', 1, id, '2024-10-01', '2025-01-15', 1 FROM `annees_scolaires` WHERE `libelle` = '2024-2025'
ON DUPLICATE KEY UPDATE `ordre` = VALUES(`ordre`), `date_debut` = VALUES(`date_debut`), `date_fin` = VALUES(`date_fin`), `statut` = VALUES(`statut`);

INSERT INTO `periodes` (`nom`, `ordre`, `annee_scolaire_id`, `date_debut`, `date_fin`, `statut`)
SELECT 'Deuxième trimestre', 2, id, '2025-01-16', '2025-04-15', 1 FROM `annees_scolaires` WHERE `libelle` = '2024-2025'
ON DUPLICATE KEY UPDATE `ordre` = VALUES(`ordre`), `date_debut` = VALUES(`date_debut`), `date_fin` = VALUES(`date_fin`), `statut` = VALUES(`statut`);

INSERT INTO `periodes` (`nom`, `ordre`, `annee_scolaire_id`, `date_debut`, `date_fin`, `statut`)
SELECT 'Troisième trimestre', 3, id, '2025-04-16', '2025-07-31', 1 FROM `annees_scolaires` WHERE `libelle` = '2024-2025'
ON DUPLICATE KEY UPDATE `ordre` = VALUES(`ordre`), `date_debut` = VALUES(`date_debut`), `date_fin` = VALUES(`date_fin`), `statut` = VALUES(`statut`);

-- Mot de passe : Admin1234!
INSERT INTO `users` (`nom`, `prenom`, `email`, `password_hash`, `role`) VALUES (
    'Admin', 'SGE', 'admin@sge.tg',
    '$2y$12$BPifpEYk4aA7Bf/78rfVE.hGcBB6PgM7JBtq1v/5Bm0kSur/A01JO',
    'admin'
);

INSERT INTO `parametres` (`cle`, `valeur`) VALUES
('nom_ecole',           'Ecole .......'),
('adresse',             'Lomé, Togo'),
('telephone',           '+228 00 00 00 00'),
('email',               'contact@ecole.tg'),
('logo',                ''),
('devise',              'FCFA'),
('frais_scol_primaire', '......'),
('frais_scol_college',  '.......'),
('frais_scol_lycee',    '.......');

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

-- Classes exemple collège / lycée
INSERT IGNORE INTO `classes` (`niveau_id`, `nom`, `niveau`, `annee_scolaire_id`, `effectif_maximum`, `statut`)
SELECT n.id, 'A', n.nom, a.id, 40, 1
FROM `niveaux` n
JOIN `annees_scolaires` a ON a.active = 1
WHERE n.nom IN ('6ème', '5ème', '4ème', '3ème', '2nde S/D', '2nde A4', '1ère D', '1ère A4', 'Terminale D', 'Terminale A4');

SELECT 'Optimisations Phase 10 appliquées.' AS message;

-- ===== ABSENCES =====
CREATE TABLE IF NOT EXISTS `absences` (
    `id`          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `eleve_id`    INT UNSIGNED    NOT NULL,
    `date_absence` DATE           NOT NULL,
    `heure_debut` TIME            DEFAULT NULL,
    `heure_fin`   TIME            DEFAULT NULL,
    `motif`       ENUM('maladie','familial','non_justifie','autre') NOT NULL DEFAULT 'non_justifie',
    `justifiee`   TINYINT(1)      NOT NULL DEFAULT 0,
    `commentaire` VARCHAR(255)    DEFAULT NULL,
    `saisie_par`  INT UNSIGNED    NOT NULL,
    `created_at`  TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_eleve`  (`eleve_id`),
    INDEX `idx_date`   (`date_absence`),
    CONSTRAINT `fk_absences_eleve`
        FOREIGN KEY (`eleve_id`) REFERENCES `eleves`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_absences_user`
        FOREIGN KEY (`saisie_par`) REFERENCES `users`(`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 'Table absences créée.' AS message;

-- ===== COMPTES PARENTS & COMPTES ÉLÈVES (Phase 11) =====

-- Rôle "eleve" : compte de connexion personnel pour l'élève
ALTER TABLE `users`
    MODIFY COLUMN `role` ENUM('admin','professeur','parent','eleve') NOT NULL DEFAULT 'parent';

-- Téléphone du parent/tuteur (SMS + création rapide de compte)
ALTER TABLE `users`
    ADD COLUMN IF NOT EXISTS `telephone` VARCHAR(30) DEFAULT NULL AFTER `email`;

-- Lien entre la fiche élève et son compte de connexion (peut être NULL)
ALTER TABLE `eleves`
    ADD COLUMN IF NOT EXISTS `user_id` INT UNSIGNED DEFAULT NULL AFTER `parent_id`,
    ADD UNIQUE KEY IF NOT EXISTS `uk_eleve_user` (`user_id`);

ALTER TABLE `eleves`
    ADD CONSTRAINT `fk_eleves_user`
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
        ON DELETE SET NULL ON UPDATE CASCADE;

SELECT 'Comptes parents/élèves : structure prête.' AS message;
