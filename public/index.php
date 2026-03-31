<?php
// public/index.php — Front Controller SGE

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

define('ROOT_PATH', dirname(dirname(__FILE__)));

require_once ROOT_PATH . '/config/constants.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/app/core/Router.php';
require_once ROOT_PATH . '/app/core/Controller.php';
require_once ROOT_PATH . '/app/core/Model.php';
require_once ROOT_PATH . '/app/middleware/AuthMiddleware.php';

// Autoload des modèles
foreach (glob(ROOT_PATH . '/app/models/*.php') as $model) {
    require_once $model;
}

// Session sécurisée
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
session_name(SESSION_NAME);
session_start();

// Récupérer l'URL
$url = $_GET['url'] ?? 'dashboard';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$parts = explode('/', $url);

$segment = strtolower($parts[0] ?? 'dashboard');
$method  = preg_replace('/[^a-zA-Z0-9_]/', '', $parts[1] ?? 'index');
$param   = isset($parts[2]) ? preg_replace('/[^a-zA-Z0-9_\-]/', '', $parts[2]) : null;

// Table de routage explicite
$routes = [
    'dashboard'  => 'DashboardController',
    'auth'       => 'AuthController',
    'eleves'     => 'EleveController',
    'classes'    => 'ClasseController',
    'notes'      => 'NoteController',
    'bulletins'  => 'BulletinController',
    'paiements'  => 'PaiementController',
    'parametres' => 'ParametreController',
    'export'     => 'ExportController',
    'absences'   => 'AbsenceController',
    'recherche'  => 'RechercheController',
];

$controllerName = $routes[$segment] ?? null;

if (!$controllerName) {
    require_once ROOT_PATH . '/app/controllers/ErrorController.php';
    (new ErrorController())->notFound();
    exit;
}

$controllerFile = ROOT_PATH . '/app/controllers/' . $controllerName . '.php';

if (!file_exists($controllerFile)) {
    require_once ROOT_PATH . '/app/controllers/ErrorController.php';
    (new ErrorController())->notFound();
    exit;
}

try {
    require_once $controllerFile;
    $controller = new $controllerName();
    if (method_exists($controller, $method)) {
        $controller->$method($param);
    } else {
        $controller->index($param);
    }
} catch (PDOException $e) {
    error_log('SGE PDO Error: ' . $e->getMessage());
    http_response_code(500);
    require ROOT_PATH . '/app/views/errors/500.php';
} catch (Exception $e) {
    error_log('SGE Error: ' . $e->getMessage());
    http_response_code(500);
    require ROOT_PATH . '/app/views/errors/500.php';
}
