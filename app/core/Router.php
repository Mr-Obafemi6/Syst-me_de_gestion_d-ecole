<?php
// app/core/Router.php — Utilitaires de routage

class Router {

    /**
     * Redirige vers une URL interne
     */
    public static function redirect(string $path): void {
        header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
        exit;
    }

    /**
     * Construit une URL interne
     */
    public static function url(string $path = ''): string {
        return BASE_URL . '/' . ltrim($path, '/');
    }

    /**
     * Retourne l'URL courante
     */
    public static function current(): string {
        return $_GET['url'] ?? 'dashboard';
    }

    /**
     * Vérifie si l'URL courante correspond à un segment
     */
    public static function is(string $segment): bool {
        $current = explode('/', self::current());
        return ($current[0] ?? '') === $segment;
    }
}
