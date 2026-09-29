<?php
// Copiez ce fichier en config/config.local.php puis adaptez les valeurs.
// config.local.php est ignoré par Git : c'est là que vivent vos identifiants.

define('DB_HOST', 'localhost');
define('DB_NAME', 'sge_db');
define('DB_USER', 'sge_user');       // évitez 'root' dès que l'appli n'est plus en local
define('DB_PASS', 'mot_de_passe_solide');

// URL publique du dossier public/ (sans slash final)
define('BASE_URL', 'http://localhost/Systemegestionecole/public');
