<?php
// Usage : php database/migrate.php  — force l'exécution des migrations de schéma
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../config/database.php';
$db = new PDO(
    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET,
    DB_USER, DB_PASS,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);
Database::migrate($db);
echo "Schéma à jour (version " . Database::SCHEMA_VERSION . ").\n";
