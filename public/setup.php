<?php
define('ROOT_PATH', dirname(dirname($_SERVER['SCRIPT_FILENAME'])));
require_once ROOT_PATH . '/config/database.php';

$db = Database::getConnection();
$hash = password_hash('Admin1234!', PASSWORD_BCRYPT, ['cost' => 12]);

$stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
$stmt->execute([$hash, 'admin@sge.tg']);

echo "Mot de passe mis à jour. <a href='../public/'>Connexion</a>";

// SUPPRIMER CE FICHIER APRÈS UTILISATION