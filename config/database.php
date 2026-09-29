<?php
// config/database.php — Connexion PDO centralisée

// Identifiants : config/config.local.php (voir config.local.example.php).
// Les valeurs ci-dessous ne servent que de repli pour un poste de développement.
if (is_file(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}
if (!defined('DB_HOST'))    define('DB_HOST', 'localhost');
if (!defined('DB_NAME'))    define('DB_NAME', 'sge_db');
if (!defined('DB_USER'))    define('DB_USER', 'root');
if (!defined('DB_PASS'))    define('DB_PASS', '');
if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');

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
                // Les migrations ne tournent qu'une fois par version de schéma
                // (1 requête légère par page au lieu d'une trentaine).
                if (self::currentSchemaVersion(self::$instance) < self::SCHEMA_VERSION) {
                    self::migrate(self::$instance);
                }
            } catch (PDOException $e) {
                // Ne jamais afficher le message brut en production
                error_log("Erreur BDD : " . $e->getMessage());
                die(json_encode(['error' => 'Connexion à la base de données impossible.']));
            }
        }
        return self::$instance;
    }

    /** À incrémenter à chaque nouvelle modification de schéma dans migrate(). */
    public const SCHEMA_VERSION = 2;

    private static function currentSchemaVersion(PDO $db): int {
        try {
            $v = $db->query("SELECT `version` FROM `schema_meta` LIMIT 1")->fetchColumn();
            return $v === false ? 0 : (int) $v;
        } catch (PDOException $e) {
            return 0; // table absente : première exécution
        }
    }

    /** Exécute toutes les migrations idempotentes puis enregistre la version. */
    public static function migrate(PDO $db): void {
        self::ensurePrimaryAcademicSchema($db);
        self::addColumnIfMissing($db, 'users', 'photo', 'VARCHAR(255) DEFAULT NULL');
        self::addColumnIfMissing($db, 'evenements', 'photo', 'VARCHAR(255) DEFAULT NULL');
        self::ensureTeacherModuleSchema($db);

        // v2 : limitation des tentatives de connexion
        $db->exec("CREATE TABLE IF NOT EXISTS `login_attempts` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `email_hash` CHAR(64) NOT NULL,
            `ip` VARCHAR(45) NOT NULL,
            `attempted_at` DATETIME NOT NULL,
            INDEX `idx_email_time` (`email_hash`, `attempted_at`),
            INDEX `idx_ip_time` (`ip`, `attempted_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $db->exec("CREATE TABLE IF NOT EXISTS `schema_meta` (
            `version` INT UNSIGNED NOT NULL,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $db->exec("DELETE FROM `schema_meta`");
        $db->prepare("INSERT INTO `schema_meta` (`version`) VALUES (?)")->execute([self::SCHEMA_VERSION]);
    }

    /** Équivalent portable (MySQL 8 + MariaDB) de ADD COLUMN IF NOT EXISTS. */
    private static function addColumnIfMissing(PDO $db, string $table, string $column, string $definition): void {
        $exists = $db->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?");
        $exists->execute([$table, $column]);
        if ((int) $exists->fetchColumn() === 0) {
            $db->exec("ALTER TABLE `$table` ADD COLUMN `$column` $definition");
        }
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

        self::ensureTeacherModuleSchema($db);

        $niveauColumns = $db->query("SHOW COLUMNS FROM `niveaux`")->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('cycle', $niveauColumns, true)) {
            $db->exec("ALTER TABLE `niveaux` ADD COLUMN `cycle` ENUM('college','lycee') NOT NULL DEFAULT 'college' AFTER `ordre`");
        }

        $db->exec("CREATE TABLE IF NOT EXISTS `matieres` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `nom` VARCHAR(100) NOT NULL,
            `code` VARCHAR(20) DEFAULT NULL,
            `description` TEXT DEFAULT NULL,
            `coefficient` DECIMAL(4,2) DEFAULT NULL,
            `volume_horaire` DECIMAL(5,2) DEFAULT NULL,
            `annee_scolaire_id` INT UNSIGNED NOT NULL,
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
        $db->exec("UPDATE `matieres` m LEFT JOIN `classes` c ON c.id = m.classe_id SET m.classe_id = NULL WHERE m.classe_id IS NOT NULL AND c.id IS NULL");
        $db->exec("ALTER TABLE `matieres` MODIFY COLUMN `classe_id` INT UNSIGNED NULL");

        $db->exec("CREATE TABLE IF NOT EXISTS `classe_matieres` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `classe_id` INT UNSIGNED NOT NULL,
            `matiere_id` INT UNSIGNED NOT NULL,
            `coefficient` DECIMAL(4,2) DEFAULT NULL,
            `volume_horaire` DECIMAL(5,2) DEFAULT NULL,
            `annee_scolaire_id` INT UNSIGNED NOT NULL,
            `statut` TINYINT(1) NOT NULL DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_classe_matiere_annee` (`classe_id`, `matiere_id`, `annee_scolaire_id`),
            INDEX `idx_classe` (`classe_id`),
            INDEX `idx_matiere` (`matiere_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $associationColumns = $db->query("SHOW COLUMNS FROM `classe_matieres`")->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('volume_horaire', $associationColumns, true)) {
            $db->exec("ALTER TABLE `classe_matieres` ADD COLUMN `volume_horaire` DECIMAL(5,2) DEFAULT NULL AFTER `coefficient`");
        }
        if (!in_array('annee_scolaire_id', $associationColumns, true)) {
            $db->exec("ALTER TABLE `classe_matieres` ADD COLUMN `annee_scolaire_id` INT UNSIGNED NULL AFTER `volume_horaire`");
            $db->exec("UPDATE `classe_matieres` cm JOIN `classes` c ON c.id = cm.classe_id SET cm.annee_scolaire_id = c.annee_scolaire_id WHERE cm.annee_scolaire_id IS NULL");
            $db->exec("ALTER TABLE `classe_matieres` MODIFY COLUMN `annee_scolaire_id` INT UNSIGNED NOT NULL");
        }
        $db->exec("ALTER TABLE `classe_matieres` MODIFY COLUMN `coefficient` DECIMAL(4,2) DEFAULT NULL");
        $oldAssociationKey = (int) $db->query("SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'classe_matieres' AND INDEX_NAME = 'uk_classe_matiere'")->fetchColumn();
        if ($oldAssociationKey > 0) {
            $db->exec("ALTER TABLE `classe_matieres` DROP INDEX `uk_classe_matiere`");
        }
        $newAssociationKey = (int) $db->query("SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'classe_matieres' AND INDEX_NAME = 'uk_classe_matiere_annee'")->fetchColumn();
        if ($newAssociationKey === 0) {
            $db->exec("ALTER TABLE `classe_matieres` ADD UNIQUE KEY `uk_classe_matiere_annee` (`classe_id`, `matiere_id`, `annee_scolaire_id`)");
        }

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

    private static function ensureTeacherModuleSchema(PDO $db): void {
        $db->exec("CREATE TABLE IF NOT EXISTS `enseignants` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id` INT UNSIGNED NOT NULL,
            `matricule` VARCHAR(50) DEFAULT NULL,
            `nom` VARCHAR(100) DEFAULT NULL,
            `prenom` VARCHAR(100) DEFAULT NULL,
            `sexe` ENUM('M','F') DEFAULT NULL,
            `date_naissance` DATE DEFAULT NULL,
            `lieu_naissance` VARCHAR(150) DEFAULT NULL,
            `nationalite` VARCHAR(80) DEFAULT NULL,
            `telephone` VARCHAR(30) DEFAULT NULL,
            `email` VARCHAR(150) DEFAULT NULL,
            `adresse` TEXT DEFAULT NULL,
            `photo` VARCHAR(255) DEFAULT NULL,
            `niveau_etude` VARCHAR(100) DEFAULT NULL,
            `specialite` VARCHAR(150) DEFAULT NULL,
            `diplome` VARCHAR(150) DEFAULT NULL,
            `experience` TEXT DEFAULT NULL,
            `date_recrutement` DATE DEFAULT NULL,
            `statut_professionnel` VARCHAR(80) DEFAULT NULL,
            `type_contrat` VARCHAR(80) DEFAULT NULL,
            `telephone_professionnel` VARCHAR(30) DEFAULT NULL,
            `email_professionnel` VARCHAR(150) DEFAULT NULL,
            `compte_statut` ENUM('actif','bloque') NOT NULL DEFAULT 'actif',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_enseignants_user` (`user_id`),
            UNIQUE KEY `uk_enseignants_matricule` (`matricule`),
            INDEX `idx_enseignants_nom` (`nom`, `prenom`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $db->exec("CREATE TABLE IF NOT EXISTS `enseignant_horaires` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `teacher_id` INT UNSIGNED NOT NULL,
            `class_id` INT UNSIGNED NOT NULL,
            `subject_id` INT UNSIGNED NOT NULL,
            `jour` ENUM('Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi') NOT NULL,
            `heure_debut` TIME NOT NULL,
            `heure_fin` TIME NOT NULL,
            `salle` VARCHAR(100) DEFAULT NULL,
            `school_year_id` INT UNSIGNED NOT NULL,
            `status` TINYINT(1) NOT NULL DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            INDEX `idx_horaire_teacher` (`teacher_id`),
            INDEX `idx_horaire_classe` (`class_id`),
            CONSTRAINT `fk_horaire_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_horaire_classe` FOREIGN KEY (`class_id`) REFERENCES `classes`(`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_horaire_matiere` FOREIGN KEY (`subject_id`) REFERENCES `matieres`(`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_horaire_annee` FOREIGN KEY (`school_year_id`) REFERENCES `annees_scolaires`(`id`) ON DELETE RESTRICT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $permissionRows = $db->query("SELECT COUNT(*) FROM `permissions` WHERE `code` IN ('teachers.view','teachers.manage','schedule.view','schedule.manage')")->fetchColumn();
        if ((int) $permissionRows < 4) {
            $db->exec("INSERT INTO `permissions` (`nom`, `code`, `description`, `module`) VALUES
                ('Voir les enseignants', 'teachers.view', 'Consulter les enseignants', 'teachers'),
                ('Gérer les enseignants', 'teachers.manage', 'Créer, modifier et bloquer les enseignants', 'teachers'),
                ('Voir les emplois du temps', 'schedule.view', 'Consulter les emplois du temps', 'schedule'),
                ('Gérer les emplois du temps', 'schedule.manage', 'Créer et modifier les emplois du temps', 'schedule')
                ON DUPLICATE KEY UPDATE `nom` = VALUES(`nom`), `description` = VALUES(`description`), `module` = VALUES(`module`), `statut` = 1");
        }

        $db->exec("INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
            SELECT r.id, p.id FROM `roles` r JOIN `permissions` p ON p.code = 'teachers.view' WHERE r.code = 'admin'");
        $db->exec("INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
            SELECT r.id, p.id FROM `roles` r JOIN `permissions` p ON p.code = 'teachers.manage' WHERE r.code = 'admin'");
        $db->exec("INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
            SELECT r.id, p.id FROM `roles` r JOIN `permissions` p ON p.code IN ('teachers.view','teachers.manage') WHERE r.code = 'directeur'");
        $db->exec("INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
            SELECT r.id, p.id FROM `roles` r JOIN `permissions` p ON p.code IN ('schedule.view','schedule.manage') WHERE r.code = 'professeur'");
        $db->exec("INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
            SELECT r.id, p.id FROM `roles` r JOIN `permissions` p ON p.code IN ('teachers.view','teachers.manage','schedule.view','schedule.manage') WHERE r.code = 'admin'");
    }

    // Empêcher le clonage du singleton
    private function __clone() {}
}
