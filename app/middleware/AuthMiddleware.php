<?php
// app/middleware/AuthMiddleware.php — Contrôle d'accès centralisé

class AuthMiddleware {

    /**
     * Vérifie que l'utilisateur est connecté.
     * Redirige vers /login sinon.
     */
    public static function requireAuth(): void {
        if (empty($_SESSION['user'])) {
            Router::redirect('auth/login');
        }

        // Vérifier le timeout de session
        if (isset($_SESSION['last_activity']) &&
            (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
            session_unset();
            session_destroy();
            Router::redirect('auth/login?expired=1');
        }
        $_SESSION['last_activity'] = time();
    }

    /**
     * Vérifie que l'utilisateur a l'un des rôles autorisés.
     * @param string|array $roles  Ex: ROLE_ADMIN ou [ROLE_ADMIN, ROLE_PROF]
     */
    public static function requireRole(string|array $roles): void {
        self::requireAuth();

        $userRole = $_SESSION['user']['role'] ?? '';
        $allowed  = (array) $roles;

        if (!in_array($userRole, $allowed, true)) {
            http_response_code(403);
            require ROOT_PATH . '/app/views/errors/403.php';
            exit;
        }
    }

    /**
     * Vérifie si l'utilisateur est connecté (sans redirection)
     */
    public static function isLoggedIn(): bool {
        return !empty($_SESSION['user']);
    }

    /**
     * Retourne l'utilisateur connecté
     */
    public static function user(): ?array {
        return $_SESSION['user'] ?? null;
    }

    /**
     * Retourne le rôle de l'utilisateur connecté
     */
    public static function role(): string {
        return $_SESSION['user']['role'] ?? '';
    }

    /**
     * Vérifie si l'utilisateur a un rôle spécifique
     */
    public static function hasRole(string $role): bool {
        return self::role() === $role;
    }

    /**
     * Connecte un utilisateur (stocke en session)
     */
    public static function login(array $user): void {
        session_regenerate_id(true); // Prévient le fixation de session
        $_SESSION['user']          = $user;
        $_SESSION['last_activity'] = time();
        unset($_SESSION['user']['password_hash']); // Ne jamais garder le hash
    }

    /**
     * Déconnecte l'utilisateur
     */
    public static function logout(): void {
        session_unset();
        session_destroy();
        // Supprimer le cookie de session
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
    }
}
