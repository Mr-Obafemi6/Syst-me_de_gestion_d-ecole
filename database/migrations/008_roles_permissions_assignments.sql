-- Migration 008 : rôles, permissions et affectations enseignant
-- Migration additive : aucune donnée métier existante n'est supprimée.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `roles` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nom` VARCHAR(80) NOT NULL,
    `code` VARCHAR(50) NOT NULL,
    `description` VARCHAR(255) DEFAULT NULL,
    `statut` TINYINT(1) NOT NULL DEFAULT 1,
    `system_role` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_roles_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `permissions` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nom` VARCHAR(120) NOT NULL,
    `code` VARCHAR(100) NOT NULL,
    `description` VARCHAR(255) DEFAULT NULL,
    `module` VARCHAR(50) NOT NULL,
    `statut` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_permissions_code` (`code`),
    INDEX `idx_permissions_module` (`module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `role_permissions` (
    `role_id` INT UNSIGNED NOT NULL,
    `permission_id` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`role_id`, `permission_id`),
    CONSTRAINT `fk_role_permissions_role` FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_role_permissions_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `user_permissions` (
    `user_id` INT UNSIGNED NOT NULL,
    `permission_id` INT UNSIGNED NOT NULL,
    `effect` ENUM('allow','deny') NOT NULL,
    `created_by` INT UNSIGNED DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`, `permission_id`),
    CONSTRAINT `fk_user_permissions_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_user_permissions_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_user_permissions_creator` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `teacher_assignments` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `teacher_id` INT UNSIGNED NOT NULL,
    `class_id` INT UNSIGNED NOT NULL,
    `subject_id` INT UNSIGNED NOT NULL,
    `school_year_id` INT UNSIGNED NOT NULL,
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_teacher_assignment` (`teacher_id`, `class_id`, `subject_id`, `school_year_id`),
    INDEX `idx_teacher_assignments_teacher` (`teacher_id`),
    INDEX `idx_teacher_assignments_class_subject` (`class_id`, `subject_id`),
    CONSTRAINT `fk_teacher_assignments_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_teacher_assignments_class` FOREIGN KEY (`class_id`) REFERENCES `classes`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_teacher_assignments_subject` FOREIGN KEY (`subject_id`) REFERENCES `matieres`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_teacher_assignments_year` FOREIGN KEY (`school_year_id`) REFERENCES `annees_scolaires`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `activity_logs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `action` VARCHAR(80) NOT NULL,
    `module` VARCHAR(50) NOT NULL,
    `description` VARCHAR(500) NOT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `result` ENUM('success','denied','failure') NOT NULL DEFAULT 'success',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_activity_user_date` (`user_id`, `created_at`),
    CONSTRAINT `fk_activity_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `users`
    MODIFY COLUMN `role` ENUM('admin','directeur','professeur','secretaire','parent','eleve') NOT NULL DEFAULT 'parent';

ALTER TABLE `users`
    ADD COLUMN IF NOT EXISTS `role_id` INT UNSIGNED DEFAULT NULL AFTER `role`;

INSERT INTO `roles` (`nom`, `code`, `description`, `system_role`) VALUES
('Administrateur', 'admin', 'Accès complet au système', 1),
('Directeur', 'directeur', 'Pilotage de l’établissement', 1),
('Enseignant', 'professeur', 'Accès pédagogique selon affectations', 1),
('Secrétaire', 'secretaire', 'Gestion administrative', 1),
('Parent', 'parent', 'Accès à ses enfants', 1),
('Élève', 'eleve', 'Accès à ses propres données', 1)
ON DUPLICATE KEY UPDATE `nom` = VALUES(`nom`), `description` = VALUES(`description`), `statut` = 1;

UPDATE `users` u JOIN `roles` r ON r.code = u.role SET u.role_id = r.id WHERE u.role_id IS NULL;

INSERT INTO `permissions` (`nom`, `code`, `description`, `module`) VALUES
('Voir le tableau de bord', 'dashboard.view', 'Accéder au tableau de bord', 'dashboard'),
('Voir les élèves', 'students.view', 'Consulter les élèves autorisés', 'students'),
('Créer un élève', 'students.create', 'Créer un élève', 'students'),
('Modifier un élève', 'students.edit', 'Modifier un élève', 'students'),
('Supprimer un élève', 'students.delete', 'Désactiver un élève', 'students'),
('Voir les classes', 'classes.view', 'Consulter les classes autorisées', 'classes'),
('Créer une classe', 'classes.create', 'Créer une classe', 'classes'),
('Modifier une classe', 'classes.edit', 'Modifier une classe', 'classes'),
('Supprimer une classe', 'classes.delete', 'Désactiver une classe', 'classes'),
('Voir les matières', 'subjects.view', 'Consulter les matières autorisées', 'subjects'),
('Créer une matière', 'subjects.create', 'Créer une matière', 'subjects'),
('Modifier une matière', 'subjects.edit', 'Modifier une matière', 'subjects'),
('Supprimer une matière', 'subjects.delete', 'Désactiver une matière', 'subjects'),
('Voir les notes', 'grades.view', 'Consulter les notes autorisées', 'grades'),
('Saisir les notes', 'grades.create', 'Saisir une note', 'grades'),
('Modifier les notes', 'grades.edit', 'Modifier une note', 'grades'),
('Supprimer les notes', 'grades.delete', 'Supprimer une note', 'grades'),
('Publier les notes', 'grades.publish', 'Publier les notes', 'grades'),
('Voir les absences', 'attendance.view', 'Consulter les absences autorisées', 'attendance'),
('Créer une absence', 'attendance.create', 'Enregistrer une absence', 'attendance'),
('Modifier une absence', 'attendance.edit', 'Modifier une absence', 'attendance'),
('Supprimer une absence', 'attendance.delete', 'Supprimer une absence', 'attendance'),
('Voir les bulletins', 'report_cards.view', 'Consulter les bulletins autorisés', 'report_cards'),
('Créer un bulletin', 'report_cards.create', 'Créer un bulletin', 'report_cards'),
('Modifier un bulletin', 'report_cards.edit', 'Modifier un bulletin', 'report_cards'),
('Imprimer les bulletins', 'report_cards.print', 'Imprimer un bulletin', 'report_cards'),
('Exporter les bulletins', 'report_cards.export', 'Exporter les bulletins', 'report_cards'),
('Voir les événements', 'events.view', 'Consulter les événements', 'events'),
('Créer un événement', 'events.create', 'Créer un événement', 'events'),
('Modifier un événement', 'events.edit', 'Modifier un événement', 'events'),
('Supprimer un événement', 'events.delete', 'Supprimer un événement', 'events'),
('Voir les notifications', 'notifications.view', 'Consulter les notifications', 'notifications'),
('Créer des notifications', 'notifications.create', 'Créer une notification', 'notifications'),
('Envoyer des notifications', 'notifications.send', 'Envoyer une notification', 'notifications'),
('Voir les utilisateurs', 'users.view', 'Consulter les utilisateurs', 'users'),
('Créer un utilisateur', 'users.create', 'Créer un utilisateur', 'users'),
('Modifier un utilisateur', 'users.edit', 'Modifier un utilisateur', 'users'),
('Supprimer un utilisateur', 'users.delete', 'Désactiver un utilisateur', 'users'),
('Voir les rôles', 'roles.view', 'Consulter les rôles', 'roles'),
('Créer un rôle', 'roles.create', 'Créer un rôle', 'roles'),
('Modifier un rôle', 'roles.edit', 'Modifier un rôle', 'roles'),
('Supprimer un rôle', 'roles.delete', 'Désactiver un rôle', 'roles'),
('Voir les permissions', 'permissions.view', 'Consulter les permissions', 'permissions'),
('Attribuer des permissions', 'permissions.assign', 'Attribuer des permissions', 'permissions'),
('Voir les paramètres', 'settings.view', 'Consulter les paramètres', 'settings'),
('Modifier les paramètres', 'settings.edit', 'Modifier les paramètres', 'settings'),
('Voir les finances', 'finance.view', 'Consulter les finances', 'finance'),
('Créer une opération financière', 'finance.create', 'Créer une opération financière', 'finance'),
('Modifier une opération financière', 'finance.edit', 'Modifier une opération financière', 'finance'),
('Supprimer une opération financière', 'finance.delete', 'Supprimer une opération financière', 'finance'),
('Voir les affectations', 'assignments.view', 'Consulter les affectations enseignant', 'assignments'),
('Gérer les affectations', 'assignments.manage', 'Gérer les affectations enseignant', 'assignments'),
('Voir SYSCET', 'syscet.view', 'Permission réservée au futur module SYSCET', 'syscet'),
('Importer SYSCET', 'syscet.import', 'Permission réservée au futur module SYSCET', 'syscet'),
('Exporter SYSCET', 'syscet.export', 'Permission réservée au futur module SYSCET', 'syscet'),
('Synchroniser SYSCET', 'syscet.sync', 'Permission réservée au futur module SYSCET', 'syscet')
ON DUPLICATE KEY UPDATE `nom` = VALUES(`nom`), `description` = VALUES(`description`), `module` = VALUES(`module`), `statut` = 1;

INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `roles` r CROSS JOIN `permissions` p WHERE r.code = 'admin';

INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `roles` r JOIN `permissions` p ON p.code IN (
    'dashboard.view','classes.view','subjects.view','grades.view','grades.create','grades.edit',
    'attendance.view','attendance.create','attendance.edit','report_cards.view','report_cards.print',
    'events.view','notifications.view','assignments.view'
) WHERE r.code = 'professeur';

INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `roles` r JOIN `permissions` p ON p.code IN ('dashboard.view','students.view','students.create','students.edit','classes.view','subjects.view','users.view','events.view','settings.view') WHERE r.code = 'directeur';

INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `roles` r JOIN `permissions` p ON p.code IN ('dashboard.view','students.view','students.create','students.edit','classes.view','subjects.view','events.view','attendance.view','attendance.create','finance.view') WHERE r.code = 'secretaire';

-- Les rôles parent et élève conservent leurs espaces existants et ne reçoivent
-- pas de permissions administratives par défaut.
