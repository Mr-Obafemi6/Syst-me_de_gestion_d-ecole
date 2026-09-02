-- Migration : Comptes de connexion Élèves + amélioration des comptes Parents
-- 2026-07-24
-- À exécuter une seule fois sur une base SGE existante.

-- 1) Ajout du rôle "eleve" (compte de connexion pour l'élève lui-même)
ALTER TABLE `users`
    MODIFY COLUMN `role` ENUM('admin','professeur','parent','eleve') NOT NULL DEFAULT 'parent';

-- 2) Lien entre un élève (fiche) et son compte de connexion (users)
--    Un élève peut ne pas avoir de compte -> user_id NULL
ALTER TABLE `eleves`
    ADD COLUMN IF NOT EXISTS `user_id` INT UNSIGNED DEFAULT NULL AFTER `parent_id`,
    ADD UNIQUE KEY IF NOT EXISTS `uk_eleve_user` (`user_id`);

-- La contrainte est ajoutée séparément pour rester compatible avec les
-- versions de MySQL/MariaDB qui n'acceptent pas IF NOT EXISTS sur ADD CONSTRAINT.
-- Si la contrainte existe déjà, ignorez l'erreur "Duplicate foreign key".
ALTER TABLE `eleves`
    ADD CONSTRAINT `fk_eleves_user`
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
        ON DELETE SET NULL ON UPDATE CASCADE;

-- 3) Téléphone du parent/tuteur (utile pour SMS + création rapide de compte parent)
ALTER TABLE `users`
    ADD COLUMN IF NOT EXISTS `telephone` VARCHAR(30) DEFAULT NULL AFTER `email`;

SELECT 'Migration 003 : comptes parents/élèves appliquée.' AS message;
