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
        $sharedData = $this->buildSharedViewData();

        // Extraire les données comme variables locales
        extract(array_merge($sharedData, $data));

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
     * Prépare les variables de vue communes au shell global
     */
    protected function buildSharedViewData(): array {
        $user = AuthMiddleware::user();
        $userInitials = 'SG';
        if ($user) {
            $first = trim($user['prenom'] ?? '');
            $last = trim($user['nom'] ?? '');
            $userInitials = strtoupper(substr($first, 0, 1) . substr($last, 0, 1));
            if ($userInitials === '') {
                $userInitials = 'SG';
            }
        }

        return [
            'app_logo' => $this->getAppSetting('logo'),
            'app_name' => $this->getAppSetting('nom_ecole', 'SGE'),
            'current_user_photo' => $user['photo'] ?? null,
            'current_user_initials' => $userInitials,
        ];
    }

    /**
     * Récupère une valeur d’un paramètre de l’établissement
     */
    protected function getAppSetting(string $key, string $default = ''): string {
        static $params = null;
        if ($params === null) {
            $params = [];
            try {
                $db = Database::getConnection();
                $stmt = $db->query("SELECT cle, valeur FROM `parametres`");
                foreach ($stmt->fetchAll() as $row) {
                    $params[$row['cle']] = (string) $row['valeur'];
                }
            } catch (Throwable $e) {
                $params = [];
            }
        }
        return $params[$key] ?? $default;
    }

    /**
     * Enregistre un fichier uploadé dans public/uploads/{sous-dossier}
     */
    protected function storeUploadedFile(array $file, string $subdir, array $allowedTypes = ['image/jpeg','image/png','image/webp','image/gif'], int $maxSize = 2097152): ?string {
        if (empty($file['name']) || empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return null;
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return null;
        }

        if (($file['size'] ?? 0) > $maxSize) {
            return null;
        }

        $mime = strtolower((string) ($file['type'] ?? ''));
        if ($mime === '') {
            $mime = strtolower((string) mime_content_type($file['tmp_name']));
        }

        if (!in_array($mime, $allowedTypes, true)) {
            return null;
        }

        $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        if ($extension === '') {
            $extension = 'jpg';
        }

        $targetDir = PUBLIC_PATH . '/uploads/' . trim($subdir, '/');
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $targetFile = $targetDir . '/' . uniqid('img_', true) . '.' . $extension;
        if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
            return null;
        }

        return 'uploads/' . trim($subdir, '/') . '/' . basename($targetFile);
    }

    /**
     * Supprime un fichier uploadé s’il existe
     */
    protected function removeUploadedFile(?string $path): void {
        if (empty($path)) {
            return;
        }

        $absolutePath = PUBLIC_PATH . '/' . ltrim($path, '/');
        if (is_file($absolutePath)) {
            unlink($absolutePath);
        }
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
