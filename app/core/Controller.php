<?php
// app/core/Controller.php — Contrôleur de base

class Controller {

    /**
     * Charge et affiche une vue avec des données
     * @param string $view    Chemin relatif depuis app/views/ (ex: 'eleves/liste')
     * @param array  $data    Variables à injecter dans la vue
     * @param string $layout  Layout à utiliser (défaut : 'main')
     */
    protected function render(string $view, array $data = [], string $layout = 'main'): void {
        // Extraire les données comme variables locales
        extract($data);

        // Chemin de la vue
        $viewFile = ROOT_PATH . '/app/views/' . $view . '.php';

        if (!file_exists($viewFile)) {
            die("Vue introuvable : " . htmlspecialchars($view));
        }

        // Capturer le contenu de la vue
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Charger le layout
        $layoutFile = ROOT_PATH . '/app/views/layouts/' . $layout . '.php';
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
    }

    /**
     * Retourne une réponse JSON (pour les endpoints API)
     */
    protected function json(mixed $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Génère un token CSRF et le stocke en session
     */
    protected function generateCsrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Valide le token CSRF d'une requête POST
     */
    protected function validateCsrf(): bool {
        $token = $_POST['csrf_token'] ?? '';
        if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(403);
            die("Token CSRF invalide.");
        }
        return true;
    }

    /**
     * Vérifie qu'une méthode HTTP est bien celle attendue
     */
    protected function requireMethod(string $method): void {
        if ($_SERVER['REQUEST_METHOD'] !== strtoupper($method)) {
            http_response_code(405);
            die("Méthode non autorisée.");
        }
    }

    /**
     * Récupère et nettoie une valeur POST
     */
    protected function post(string $key, mixed $default = null): mixed {
        return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
    }

    /**
     * Récupère et nettoie une valeur GET
     */
    protected function get(string $key, mixed $default = null): mixed {
        return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
    }

    /**
     * Stocke un message flash en session
     */
    protected function flash(string $type, string $message): void {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    /**
     * Récupère et supprime le message flash
     */
    protected function getFlash(): ?array {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }
}
