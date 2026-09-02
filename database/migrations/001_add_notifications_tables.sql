-- Système de Notifications SGE
-- Migration: 2026-06-03

-- Table notifications : enregistre chaque notification
CREATE TABLE IF NOT EXISTS `notifications` (
    `id` INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `recipient_id` INT UNSIGNED NOT NULL,
    `event_type` ENUM('note.created', 'absence.created', 'absence.justified', 'payment.created', 'payment.cancelled') NOT NULL,
    `related_entity_type` ENUM('eleve', 'note', 'absence', 'paiement') NOT NULL,
    `related_entity_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `body` TEXT NOT NULL,
    `data_json` JSON,
    `read_at` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_recipient_read (recipient_id, read_at),
    CONSTRAINT fk_notifications_recipient FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table notification_logs : enregistre les tentatives d'envoi
CREATE TABLE IF NOT EXISTS `notification_logs` (
    `id` INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `notification_id` INT UNSIGNED,
    `channel` ENUM('email', 'in_app', 'sms') NOT NULL,
    `status` ENUM('sent', 'failed', 'pending') NOT NULL DEFAULT 'pending',
    `error_message` VARCHAR(500),
    `sent_at` DATETIME,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notification_logs_id FOREIGN KEY (notification_id) REFERENCES notifications(id) ON DELETE CASCADE,
    INDEX idx_notification_channel (notification_id, channel)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table notification_preferences : permet opt-in/opt-out (phase 2)
CREATE TABLE IF NOT EXISTS `notification_preferences` (
    `id` INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL UNIQUE,
    `email_enabled` BOOLEAN DEFAULT 1,
    `in_app_enabled` BOOLEAN DEFAULT 1,
    `sms_enabled` BOOLEAN DEFAULT 0,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_notification_preferences_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
