-- Migration 005 : Suppression du module SYSCET
-- Supprime les objets Syscet éventuellement installés par la migration 004.

DROP TABLE IF EXISTS `eleve_syscet_historique`;
DROP TABLE IF EXISTS `eleve_syscet_contacts`;
DROP TABLE IF EXISTS `eleve_syscet_infos`;
DROP TABLE IF EXISTS `journal_activites`;

SET @has_syscet_id = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'classes'
      AND COLUMN_NAME = 'syscet_id'
);

SET @drop_syscet_id = IF(
    @has_syscet_id > 0,
    'ALTER TABLE `classes` DROP COLUMN `syscet_id`',
    'SELECT 1'
);
PREPARE stmt FROM @drop_syscet_id;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
