<?php
// config/constants.php

if (!defined('BASE_URL')) {
    // Nom du dossier avec espace et accent — encodage URL obligatoire
    define('BASE_URL', 'http://localhost/SGE/public');
}
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}
if (!defined('APP_PATH'))    define('APP_PATH',    ROOT_PATH . '/app');
if (!defined('CONFIG_PATH')) define('CONFIG_PATH', ROOT_PATH . '/config');
if (!defined('LIB_PATH'))    define('LIB_PATH',    ROOT_PATH . '/lib');
if (!defined('PUBLIC_PATH')) define('PUBLIC_PATH', ROOT_PATH . '/public');

define('SESSION_NAME',    'SGE_SESSION');
define('SESSION_TIMEOUT', 3600);

define('ROLE_ADMIN',  'admin');
define('ROLE_PROF',   'professeur');
define('ROLE_PARENT', 'parent');

define('ITEMS_PER_PAGE', 20);
define('DEVISE',         'FCFA');
define('APP_VERSION',    '1.0.0');
define('APP_NAME',       "SGE — Système de Gestion d'École");
