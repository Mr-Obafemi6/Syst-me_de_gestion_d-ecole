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
                self::ensurePrimaryAcademicSchema(self::$instance);
                self::$instance->exec("ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `photo` VARCHAR(255) DEFAULT NULL");
                self::$instance->exec("ALTER TABLE `evenements` ADD COLUMN IF NOT EXISTS `photo` VARCHAR(255) DEFAULT NULL");
            } catch (PDOException $e) {
                // Ne jamais afficher le message brut en production
                error_log("Erreur BDD : " . $e->getMessage());
                die(json_encode(['error' => 'Connexion à la base de données impossible.']));
            }
        }
        return self::$instance;
    }

    private static function ensurePrimaryAcademicSchema(PDO $db): void {
        $db->exec("CREATE TABLE IF NOT EXISTS `niveaux` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `nom` VARCHAR(30) NOT NULL,
            `ordre` TINYINT UNSIGNED NOT NULL,
            `cycle` ENUM('college','lycee') NOT NULL DEFAULT 'college',
            `statut` TINYINT(1) NOT NULL DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_niveau_nom` (`nom`),
            UNIQUE KEY `uk_niveau_ordre` (`ordre`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $niveauColumns = $db->query("SHOW COLUMNS FROM `niveaux`")->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('cycle', $niveauColumns, true)) {
            $db->exec("ALTER TABLE `niveaux` ADD COLUMN `cycle` ENUM('college','lycee') NOT NULL DEFAULT 'college' AFTER `ordre`");
        }

        $db->exec("CREATE TABLE IF NOT EXISTS `matieres` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `nom` VARCHAR(100) NOT NULL,
            `code` VARCHAR(20) DEFAULT NULL,
            `description` TEXT DEFAULT NULL,
            `coefficient` DECIMAL(4,2) NOT NULL DEFAULT 1.00,
            `classe_id` INT UNSIGNED DEFAULT NULL,
            `prof_id` INT UNSIGNED DEFAULT NULL,
            `statut` TINYINT(1) NOT NULL DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_matiere_nom` (`nom`),
            INDEX `idx_classe` (`classe_id`),
            INDEX `idx_prof` (`prof_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // Repair existing matieres table if it lacks columns
        $matiereColumns = $db->query("SHOW COLUMNS FROM `matieres`")->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('code', $matiereColumns, true)) {
            $db->exec("ALTER TABLE `matieres` ADD COLUMN `code` VARCHAR(20) DEFAULT NULL AFTER `nom`");
        }
        if (!in_array('description', $matiereColumns, true)) {
            $db->exec("ALTER TABLE `matieres` ADD COLUMN `description` TEXT DEFAULT NULL AFTER `code`");
        }
        if (!in_array('prof_id', $matiereColumns, true)) {
            $db->exec("ALTER TABLE `matieres` ADD COLUMN `prof_id` INT UNSIGNED DEFAULT NULL AFTER `classe_id`");
        }
        if (!in_array('statut', $matiereColumns, true)) {
            $db->exec("ALTER TABLE `matieres` ADD COLUMN `statut` TINYINT(1) NOT NULL DEFAULT 1 AFTER `prof_id`");
        }
        if (!in_array('updated_at', $matiereColumns, true)) {
            $db->exec("ALTER TABLE `matieres` ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`");
        }

        $db->exec("CREATE TABLE IF NOT EXISTS `classe_matieres` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `classe_id` INT UNSIGNED NOT NULL,
            `matiere_id` INT UNSIGNED NOT NULL,
            `coefficient` DECIMAL(4,2) NOT NULL DEFAULT 1.00,
            `statut` TINYINT(1) NOT NULL DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_classe_matiere` (`classe_id`, `matiere_id`),
            INDEX `idx_classe` (`classe_id`),
            INDEX `idx_matiere` (`matiere_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $db->exec("CREATE TABLE IF NOT EXISTS `periodes` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `nom` VARCHAR(100) NOT NULL,
            `ordre` TINYINT UNSIGNED NOT NULL,
            `annee_scolaire_id` INT UNSIGNED NOT NULL,
            `date_debut` DATE DEFAULT NULL,
            `date_fin` DATE DEFAULT NULL,
            `statut` TINYINT(1) NOT NULL DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_periode_annee_nom` (`annee_scolaire_id`, `nom`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $columns = $db->query("SHOW COLUMNS FROM `classes`")->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('niveau_id', $columns, true)) {
            $db->exec("ALTER TABLE `classes` ADD COLUMN `niveau_id` INT UNSIGNED NULL AFTER `id`");
        }
        if (!in_array('enseignant_id', $columns, true)) {
            $db->exec("ALTER TABLE `classes` ADD COLUMN `enseignant_id` INT UNSIGNED NULL AFTER `annee_scolaire_id`");
        }
        if (!in_array('effectif_maximum', $columns, true)) {
            $db->exec("ALTER TABLE `classes` ADD COLUMN `effectif_maximum` INT UNSIGNED NOT NULL DEFAULT 30 AFTER `enseignant_id`");
        }
        if (!in_array('statut', $columns, true)) {
            $db->exec("ALTER TABLE `classes` ADD COLUMN `statut` TINYINT(1) NOT NULL DEFAULT 1 AFTER `effectif_maximum`");
        }
        if (!in_array('updated_at', $columns, true)) {
            $db->exec("ALTER TABLE `classes` ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`");
        }

        $niveauxCount = (int) $db->query("SELECT COUNT(*) FROM `niveaux`")->fetchColumn();
        if ($niveauxCount === 0) {
            $db->exec("INSERT INTO `niveaux` (`nom`, `ordre`, `cycle`, `statut`) VALUES
                ('6ème', 1, 'college', 1), ('5ème', 2, 'college', 1), ('4ème', 3, 'college', 1), ('3ème', 4, 'college', 1),
                ('2nde S/D', 5, 'lycee', 1), ('2nde A4', 6, 'lycee', 1),
                ('1ère D', 7, 'lycee', 1), ('1ère A4', 8, 'lycee', 1),
                ('Terminale D', 9, 'lycee', 1), ('Terminale A4', 10, 'lycee', 1)");
        }

        $anneeActive = $db->query("SELECT id FROM `annees_scolaires` WHERE active = 1 LIMIT 1")->fetch();
        if ($anneeActive) {
            $periodeCount = (int) $db->query("SELECT COUNT(*) FROM `periodes` WHERE annee_scolaire_id = " . (int) $anneeActive['id'])->fetchColumn();
            if ($periodeCount === 0) {
                $db->exec("INSERT INTO `periodes` (`nom`, `ordre`, `annee_scolaire_id`, `date_debut`, `date_fin`, `statut`) VALUES
                    ('Premier trimestre', 1, " . (int) $anneeActive['id'] . ", '2024-10-01', '2025-01-15', 1),
                    ('Deuxième trimestre', 2, " . (int) $anneeActive['id'] . ", '2025-01-16', '2025-04-15', 1),
                    ('Troisième trimestre', 3, " . (int) $anneeActive['id'] . ", '2025-04-16', '2025-07-31', 1)");
            }
        }
    }

    // Empêcher le clonage du singleton
    private function __clone() {}
}
