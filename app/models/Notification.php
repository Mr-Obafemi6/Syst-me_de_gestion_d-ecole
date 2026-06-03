<?php
// app/models/Notification.php

require_once ROOT_PATH . '/app/core/Model.php';

class Notification extends Model {
    protected string $table = 'notifications';

    /**
     * Récupère les notifications d'un utilisateur avec pagination
     */
    public function forUser(int $userId, int $limit = 50, int $offset = 0): array {
        return $this->query(
            "SELECT * FROM `{$this->table}` WHERE `recipient_id` = ? ORDER BY `created_at` DESC LIMIT ? OFFSET ?",
            [$userId, $limit, $offset]
        ) ?? [];
    }

    /**
     * Compte les notifications non-lues d'un utilisateur
     */
    public function getUnreadCount(int $userId): int {
        $result = $this->queryOne(
            "SELECT COUNT(*) as cnt FROM `{$this->table}` WHERE `recipient_id` = ? AND `read_at` IS NULL",
            [$userId]
        );
        return $result['cnt'] ?? 0;
    }

    /**
     * Récupère les dernières notifications non-lues
     */
    public function getRecentUnread(int $userId, int $limit = 5): array {
        return $this->query(
            "SELECT * FROM `{$this->table}` WHERE `recipient_id` = ? AND `read_at` IS NULL ORDER BY `created_at` DESC LIMIT ?",
            [$userId, $limit]
        ) ?? [];
    }

    /**
     * Marque une notification comme lue
     */
    public function markAsRead(int $notificationId): bool {
        return $this->update($notificationId, ['read_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Marque toutes les notifications d'un utilisateur comme lues
     */
    public function markAllAsRead(int $userId): bool {
        try {
            $db = Database::getConnection();
            $db->prepare("UPDATE `{$this->table}` SET `read_at` = ? WHERE `recipient_id` = ? AND `read_at` IS NULL")
                ->execute([date('Y-m-d H:i:s'), $userId]);
            return true;
        } catch (Exception $e) {
            error_log('Error marking notifications as read: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprime les notifications lues plus de 30 jours
     */
    public function cleanupOldNotifications(): int {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("DELETE FROM `{$this->table}` WHERE `read_at` IS NOT NULL AND `read_at` < DATE_SUB(NOW(), INTERVAL 30 DAY)");
            $stmt->execute();
            return $stmt->rowCount();
        } catch (Exception $e) {
            error_log('Error cleaning up notifications: ' . $e->getMessage());
            return 0;
        }
    }
}
