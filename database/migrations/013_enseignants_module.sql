CREATE TABLE IF NOT EXISTS `enseignants` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `matricule` VARCHAR(50) DEFAULT NULL,
    `nom` VARCHAR(100) DEFAULT NULL,
    `prenom` VARCHAR(100) DEFAULT NULL,
    `sexe` ENUM('M','F') DEFAULT NULL,
    `date_naissance` DATE DEFAULT NULL,
    `lieu_naissance` VARCHAR(150) DEFAULT NULL,
    `nationalite` VARCHAR(80) DEFAULT NULL,
    `telephone` VARCHAR(30) DEFAULT NULL,
    `email` VARCHAR(150) DEFAULT NULL,
    `adresse` TEXT DEFAULT NULL,
    `photo` VARCHAR(255) DEFAULT NULL,
    `niveau_etude` VARCHAR(100) DEFAULT NULL,
    `specialite` VARCHAR(150) DEFAULT NULL,
    `diplome` VARCHAR(150) DEFAULT NULL,
    `experience` TEXT DEFAULT NULL,
    `date_recrutement` DATE DEFAULT NULL,
    `statut_professionnel` VARCHAR(80) DEFAULT NULL,
    `type_contrat` VARCHAR(80) DEFAULT NULL,
    `telephone_professionnel` VARCHAR(30) DEFAULT NULL,
    `email_professionnel` VARCHAR(150) DEFAULT NULL,
    `compte_statut` ENUM('actif','bloque') NOT NULL DEFAULT 'actif',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_enseignants_user` (`user_id`),
    UNIQUE KEY `uk_enseignants_matricule` (`matricule`),
    INDEX `idx_enseignants_nom` (`nom`, `prenom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `enseignant_horaires` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `teacher_id` INT UNSIGNED NOT NULL,
    `class_id` INT UNSIGNED NOT NULL,
    `subject_id` INT UNSIGNED NOT NULL,
    `jour` ENUM('Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi') NOT NULL,
    `heure_debut` TIME NOT NULL,
    `heure_fin` TIME NOT NULL,
    `salle` VARCHAR(100) DEFAULT NULL,
    `school_year_id` INT UNSIGNED NOT NULL,
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_horaire_teacher` (`teacher_id`),
    INDEX `idx_horaire_classe` (`class_id`),
    CONSTRAINT `fk_horaire_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_horaire_classe` FOREIGN KEY (`class_id`) REFERENCES `classes`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_horaire_matiere` FOREIGN KEY (`subject_id`) REFERENCES `matieres`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_horaire_annee` FOREIGN KEY (`school_year_id`) REFERENCES `annees_scolaires`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `permissions` (`nom`, `code`, `description`, `module`) VALUES
('Voir les enseignants', 'teachers.view', 'Consulter les enseignants', 'teachers')
ON DUPLICATE KEY UPDATE `nom` = VALUES(`nom`), `description` = VALUES(`description`), `module` = VALUES(`module`), `statut` = 1;

INSERT INTO `permissions` (`nom`, `code`, `description`, `module`) VALUES
('Gérer les enseignants', 'teachers.manage', 'Créer, modifier et bloquer les enseignants', 'teachers')
ON DUPLICATE KEY UPDATE `nom` = VALUES(`nom`), `description` = VALUES(`description`), `module` = VALUES(`module`), `statut` = 1;

INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `roles` r JOIN `permissions` p ON p.code = 'teachers.view' WHERE r.code = 'admin';

INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `roles` r JOIN `permissions` p ON p.code = 'teachers.manage' WHERE r.code = 'admin';

INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `roles` r JOIN `permissions` p ON p.code = 'teachers.view' WHERE r.code = 'directeur';

INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `roles` r JOIN `permissions` p ON p.code IN ('teachers.view', 'teachers.manage') WHERE r.code = 'professeur';

INSERT INTO `permissions` (`nom`, `code`, `description`, `module`) VALUES
('Voir les emplois du temps', 'schedule.view', 'Consulter les emplois du temps', 'schedule')
ON DUPLICATE KEY UPDATE `nom` = VALUES(`nom`), `description` = VALUES(`description`), `module` = VALUES(`module`), `statut` = 1;

INSERT INTO `permissions` (`nom`, `code`, `description`, `module`) VALUES
('Gérer les emplois du temps', 'schedule.manage', 'Créer et modifier les emplois du temps', 'schedule')
ON DUPLICATE KEY UPDATE `nom` = VALUES(`nom`), `description` = VALUES(`description`), `module` = VALUES(`module`), `statut` = 1;

INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `roles` r JOIN `permissions` p ON p.code IN ('schedule.view','schedule.manage') WHERE r.code = 'professeur';

INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `roles` r JOIN `permissions` p ON p.code IN ('schedule.view','schedule.manage') WHERE r.code = 'admin';
