-- Migration 007 : configuration des matières et coefficients par classe
-- Non destructive : conserve les classes, matières et associations existantes.

SET NAMES utf8mb4;

SET @has_volume = (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'classe_matieres'
      AND COLUMN_NAME = 'volume_horaire'
);
SET @sql = IF(@has_volume = 0,
    'ALTER TABLE `classe_matieres` ADD COLUMN `volume_horaire` DECIMAL(5,2) DEFAULT NULL AFTER `coefficient`',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_annee = (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'classe_matieres'
      AND COLUMN_NAME = 'annee_scolaire_id'
);
SET @sql = IF(@has_annee = 0,
    'ALTER TABLE `classe_matieres` ADD COLUMN `annee_scolaire_id` INT UNSIGNED NULL AFTER `volume_horaire`',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

UPDATE `classe_matieres` cm
JOIN `classes` c ON c.id = cm.classe_id
SET cm.annee_scolaire_id = c.annee_scolaire_id
WHERE cm.annee_scolaire_id IS NULL;

ALTER TABLE `classe_matieres` MODIFY COLUMN `coefficient` DECIMAL(4,2) DEFAULT NULL;
ALTER TABLE `classe_matieres` MODIFY COLUMN `annee_scolaire_id` INT UNSIGNED NOT NULL;

SET @has_old_key = (
    SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'classe_matieres'
      AND INDEX_NAME = 'uk_classe_matiere'
);
SET @sql = IF(@has_old_key > 0,
    'ALTER TABLE `classe_matieres` DROP INDEX `uk_classe_matiere`',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_new_key = (
    SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'classe_matieres'
      AND INDEX_NAME = 'uk_classe_matiere_annee'
);
SET @sql = IF(@has_new_key = 0,
    'ALTER TABLE `classe_matieres` ADD UNIQUE KEY `uk_classe_matiere_annee` (`classe_id`, `matiere_id`, `annee_scolaire_id`)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Un seul catalogue réutilisable. Les codes existants servent d'identifiants stables.
UPDATE `matieres` m
LEFT JOIN `classes` c ON c.id = m.classe_id
SET m.classe_id = NULL
WHERE m.classe_id IS NOT NULL AND c.id IS NULL;

ALTER TABLE `matieres` MODIFY COLUMN `classe_id` INT UNSIGNED NULL;

UPDATE `matieres` SET `nom` = 'Français', `description` = 'Français' WHERE `code` = 'FR';
UPDATE `matieres` SET `nom` = 'Mathématiques', `description` = 'Mathématiques' WHERE `code` = 'MATH';
UPDATE `matieres` SET `nom` = 'Histoire-Géographie', `description` = 'Histoire-Géographie' WHERE `code` = 'HG';
UPDATE `matieres` SET `nom` = 'Éducation civique et morale', `description` = 'Éducation civique et morale' WHERE `code` = 'EC';
UPDATE `matieres` SET `nom` = 'Physique-Chimie-Technologie', `description` = 'Physique-Chimie-Technologie' WHERE `code` = 'PC';
UPDATE `matieres` SET `nom` = 'TIC', `description` = 'Technologies de l''information et de la communication' WHERE `code` = 'INFO';
UPDATE `matieres` SET `nom` = 'Sciences de la Vie et de la Terre', `description` = 'Sciences de la Vie et de la Terre' WHERE `code` = 'SVT';
UPDATE `matieres` SET `nom` = 'Éducation Physique et Sportive', `description` = 'Éducation Physique et Sportive' WHERE `code` = 'EPS';

INSERT INTO `matieres` (`nom`, `code`, `description`, `coefficient`, `statut`)
SELECT 'Sciences Physiques', 'SP', 'Sciences Physiques', 1.00, 1
WHERE NOT EXISTS (SELECT 1 FROM `matieres` WHERE `code` = 'SP');

-- Les dix classes sont déjà gérées dans le module Classes. Cette migration
-- ne crée aucune classe afin de ne jamais produire de doublon.

-- Les coefficients déjà présents sont désactivés : ils provenaient du catalogue indicatif.
UPDATE `classe_matieres` cm
JOIN `classes` c ON c.id = cm.classe_id
SET cm.statut = 0
WHERE c.annee_scolaire_id = cm.annee_scolaire_id;

-- Coefficients confirmés : collège.
INSERT INTO `classe_matieres` (`classe_id`, `matiere_id`, `coefficient`, `volume_horaire`, `annee_scolaire_id`, `statut`)
SELECT c.id, m.id, x.coefficient, NULL, c.annee_scolaire_id, 1
FROM (
    SELECT 1 AS niveau_ordre, 'FR' AS code, 2 AS coefficient UNION ALL
    SELECT 1, 'HG', 1 UNION ALL SELECT 1, 'EC', 1 UNION ALL SELECT 1, 'ANG', 1 UNION ALL
    SELECT 1, 'MATH', 1 UNION ALL SELECT 1, 'PC', 1 UNION ALL SELECT 1, 'SVT', 1 UNION ALL SELECT 1, 'EPS', 1 UNION ALL
    SELECT 2, 'FR', 2 UNION ALL SELECT 2, 'HG', 1 UNION ALL SELECT 2, 'EC', 1 UNION ALL SELECT 2, 'ANG', 1 UNION ALL
    SELECT 2, 'MATH', 1 UNION ALL SELECT 2, 'PC', 1 UNION ALL SELECT 2, 'SVT', 1 UNION ALL SELECT 2, 'EPS', 1 UNION ALL
    SELECT 3, 'FR', 3 UNION ALL SELECT 3, 'HG', 2 UNION ALL SELECT 3, 'EC', 2 UNION ALL SELECT 3, 'ANG', 2 UNION ALL
    SELECT 3, 'MATH', 3 UNION ALL SELECT 3, 'PC', 3 UNION ALL SELECT 3, 'SVT', 2 UNION ALL SELECT 3, 'EPS', 1 UNION ALL
    SELECT 4, 'FR', 3 UNION ALL SELECT 4, 'HG', 2 UNION ALL SELECT 4, 'EC', 2 UNION ALL SELECT 4, 'ANG', 2 UNION ALL
    SELECT 4, 'MATH', 3 UNION ALL SELECT 4, 'PC', 3 UNION ALL SELECT 4, 'SVT', 2 UNION ALL SELECT 4, 'EPS', 1
) x
JOIN `niveaux` n ON n.ordre = x.niveau_ordre
JOIN `classes` c ON c.niveau_id = n.id
JOIN `matieres` m ON m.code = x.code
WHERE c.statut = 1
ON DUPLICATE KEY UPDATE coefficient = VALUES(coefficient), volume_horaire = NULL, statut = 1;

-- Coefficients confirmés : 2nde A4, 1ère D et Terminale D.
INSERT INTO `classe_matieres` (`classe_id`, `matiere_id`, `coefficient`, `volume_horaire`, `annee_scolaire_id`, `statut`)
SELECT c.id, m.id, x.coefficient, NULL, c.annee_scolaire_id, 1
FROM (
    SELECT 6 AS niveau_ordre, 'FR' AS code, 5 AS coefficient UNION ALL SELECT 6, 'ANG', 4 UNION ALL SELECT 6, 'HG', 3 UNION ALL SELECT 6, 'MATH', 3 UNION ALL SELECT 6, 'PHIL', 2 UNION ALL SELECT 6, 'SVT', 2 UNION ALL SELECT 6, 'SP', 2 UNION ALL SELECT 6, 'EC', 2 UNION ALL SELECT 6, 'EPS', 2 UNION ALL SELECT 6, 'INFO', 2 UNION ALL
    SELECT 7, 'FR', 2 UNION ALL SELECT 7, 'ANG', 2 UNION ALL SELECT 7, 'HG', 2 UNION ALL SELECT 7, 'MATH', 4 UNION ALL SELECT 7, 'PHIL', 2 UNION ALL SELECT 7, 'SVT', 3 UNION ALL SELECT 7, 'SP', 3 UNION ALL SELECT 7, 'EC', 2 UNION ALL SELECT 7, 'EPS', 2 UNION ALL SELECT 7, 'INFO', 2 UNION ALL
    SELECT 9, 'FR', 3 UNION ALL SELECT 9, 'ANG', 2 UNION ALL SELECT 9, 'HG', 2 UNION ALL SELECT 9, 'MATH', 3 UNION ALL SELECT 9, 'PHIL', 2 UNION ALL SELECT 9, 'SVT', 4 UNION ALL SELECT 9, 'SP', 3 UNION ALL SELECT 9, 'EPS', 2 UNION ALL SELECT 9, 'INFO', 2
) x
JOIN `niveaux` n ON n.ordre = x.niveau_ordre
JOIN `classes` c ON c.niveau_id = n.id
JOIN `matieres` m ON m.code = x.code
WHERE c.statut = 1
ON DUPLICATE KEY UPDATE coefficient = VALUES(coefficient), volume_horaire = NULL, statut = 1;

-- Aucun coefficient n'est inventé pour 2nde S, 1ère A4 et Terminale A4.
