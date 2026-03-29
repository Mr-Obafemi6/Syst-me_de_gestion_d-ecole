<?php
// config/database.php — Connexion PDO centralisée

define('DB_HOST', 'localhost');
define('DB_NAME', 'sge_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                // Ne jamais afficher le message brut en production
                error_log("Erreur BDD : " . $e->getMessage());
                die(json_encode(['error' => 'Connexion à la base de données impossible.']));
            }
        }
        return self::$instance;
    }

    // Empêcher le clonage du singleton
    private function __clone() {}
}
