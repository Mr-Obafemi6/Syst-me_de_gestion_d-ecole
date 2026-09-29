<?php
// app/services/LoginThrottle.php
// Limite les tentatives de connexion échouées (par compte et par adresse IP).

class LoginThrottle {
    private const WINDOW_MINUTES = 15;
    private const MAX_PER_ACCOUNT = 5;
    private const MAX_PER_IP = 20;

    private static function ip(): string {
        // REMOTE_ADDR uniquement : les en-têtes X-Forwarded-* sont falsifiables
        return substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);
    }

    private static function key(string $email): string {
        return hash('sha256', strtolower(trim($email)));
    }

    public static function isBlocked(string $email): bool {
        $db = Database::getConnection();
        $since = date('Y-m-d H:i:s', time() - self::WINDOW_MINUTES * 60);

        $stmt = $db->prepare("SELECT COUNT(*) FROM `login_attempts` WHERE `email_hash` = ? AND `attempted_at` >= ?");
        $stmt->execute([self::key($email), $since]);
        if ((int) $stmt->fetchColumn() >= self::MAX_PER_ACCOUNT) return true;

        $stmt = $db->prepare("SELECT COUNT(*) FROM `login_attempts` WHERE `ip` = ? AND `attempted_at` >= ?");
        $stmt->execute([self::ip(), $since]);
        return (int) $stmt->fetchColumn() >= self::MAX_PER_IP;
    }

    public static function recordFailure(string $email): void {
        $db = Database::getConnection();
        $db->prepare("INSERT INTO `login_attempts` (`email_hash`, `ip`, `attempted_at`) VALUES (?, ?, NOW())")
           ->execute([self::key($email), self::ip()]);
        // Nettoyage des anciennes entrées
        $db->exec("DELETE FROM `login_attempts` WHERE `attempted_at` < (NOW() - INTERVAL 1 DAY)");
    }

    public static function clear(string $email): void {
        Database::getConnection()
            ->prepare("DELETE FROM `login_attempts` WHERE `email_hash` = ?")
            ->execute([self::key($email)]);
    }

    public static function windowMinutes(): int {
        return self::WINDOW_MINUTES;
    }
}
