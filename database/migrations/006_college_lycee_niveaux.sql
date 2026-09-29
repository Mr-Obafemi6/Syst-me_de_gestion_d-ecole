-- Migration 006 : Passage du primaire au collège / lycée
-- Niveaux : 6ème → 3ème (collège) + 2nde S/D, 2nde A4, 1ère D, 1ère A4, Terminale D, Terminale A4 (lycée)

SET FOREIGN_KEY_CHECKS = 0;

-- Colonne cycle sur les niveaux
SET @has_cycle = (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'niveaux' AND COLUMN_NAME = 'cycle'
);
SET @add_cycle = IF(
    @has_cycle = 0,
    "ALTER TABLE `niveaux` ADD COLUMN `cycle` ENUM('college','lycee') NOT NULL DEFAULT 'college' AFTER `ordre`",
    'SELECT 1'
);
PREPARE stmt FROM @add_cycle;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Réinitialiser niveaux (supprime les classes orphelines si besoin — environnement dev)
DELETE FROM `classe_matieres`;
DELETE FROM `classes`;
DELETE FROM `niveaux`;

INSERT INTO `niveaux` (`nom`, `ordre`, `cycle`, `statut`) VALUES
('6ème',          1,  'college', 1),
('5ème',          2,  'college', 1),
('4ème',          3,  'college', 1),
('3ème',          4,  'college', 1),
('2nde S',        5,  'lycee',   1),
('2nde A4',       6,  'lycee',   1),
('1ère D',        7,  'lycee',   1),
('1ère A4',       8,  'lycee',   1),
('Terminale D',   9,  'lycee',   1),
('Terminale A4', 10,  'lycee',   1);

-- Catalogue matières secondaire
DELETE FROM `matieres`;

INSERT INTO `matieres` (`nom`, `code`, `description`, `coefficient`, `statut`) VALUES
('Français',              'FR',   'Français',                        3.00, 1),
('Mathématiques',         'MATH', 'Mathématiques',                   4.00, 1),
('Physique-Chimie',       'PC',   'Physique-Chimie',                 3.00, 1),
('SVT',                   'SVT',  'Sciences de la Vie et de la Terre', 2.00, 1),
('Histoire-Géographie',   'HG',   'Histoire-Géographie',             2.00, 1),
('Anglais',               'ANG',  'Anglais',                         2.00, 1),
('Philosophie',           'PHIL', 'Philosophie',                     2.00, 1),
('EPS',                   'EPS',  'Éducation Physique et Sportive',  1.00, 1),
('Espagnol',              'ESP',  'Espagnol',                        2.00, 1),
('Allemand',              'ALL',  'Allemand',                        2.00, 1),
('Informatique',          'INFO', 'Informatique',                    1.00, 1),
('Éducation civique Morale',     'ECM',   'Éducation civique',               1.00, 1);

-- Classes exemple pour l'année active
INSERT INTO `classes` (`niveau_id`, `nom`, `niveau`, `annee_scolaire_id`, `effectif_maximum`, `statut`)
SELECT n.id, 'A', n.nom, a.id, 40, 1
FROM `niveaux` n
CROSS JOIN (SELECT id FROM `annees_scolaires` WHERE active = 1 LIMIT 1) a
WHERE n.nom IN ('6ème', '5ème', '4ème', '3ème', '2nde S/D', '2nde A4', '1ère D', '1ère A4', 'Terminale D', 'Terminale A4');

-- Matières par classe (coefficients indicatifs)
INSERT INTO `classe_matieres` (`classe_id`, `matiere_id`, `coefficient`, `statut`)
SELECT c.id, m.id, cm.coef, 1
FROM `classes` c
JOIN `niveaux` n ON n.id = c.niveau_id
JOIN (
    SELECT '6ème' AS nv, 'Français' AS mat, 3 AS coef UNION ALL
    SELECT '6ème', 'Mathématiques', 4 UNION ALL
    SELECT '6ème', 'Physique-Chimie', 2 UNION ALL
    SELECT '6ème', 'SVT', 2 UNION ALL
    SELECT '6ème', 'Histoire-Géographie', 2 UNION ALL
    SELECT '6ème', 'Anglais', 2 UNION ALL
    SELECT '6ème', 'EPS', 1 UNION ALL
    SELECT '5ème', 'Français', 3 UNION ALL
    SELECT '5ème', 'Mathématiques', 4 UNION ALL
    SELECT '5ème', 'Physique-Chimie', 2 UNION ALL
    SELECT '5ème', 'SVT', 2 UNION ALL
    SELECT '5ème', 'Histoire-Géographie', 2 UNION ALL
    SELECT '5ème', 'Anglais', 2 UNION ALL
    SELECT '5ème', 'EPS', 1 UNION ALL
    SELECT '4ème', 'Français', 3 UNION ALL
    SELECT '4ème', 'Mathématiques', 4 UNION ALL
    SELECT '4ème', 'Physique-Chimie', 3 UNION ALL
    SELECT '4ème', 'SVT', 2 UNION ALL
    SELECT '4ème', 'Histoire-Géographie', 2 UNION ALL
    SELECT '4ème', 'Anglais', 2 UNION ALL
    SELECT '4ème', 'EPS', 1 UNION ALL
    SELECT '3ème', 'Français', 3 UNION ALL
    SELECT '3ème', 'Mathématiques', 4 UNION ALL
    SELECT '3ème', 'Physique-Chimie', 3 UNION ALL
    SELECT '3ème', 'SVT', 3 UNION ALL
    SELECT '3ème', 'Histoire-Géographie', 2 UNION ALL
    SELECT '3ème', 'Anglais', 2 UNION ALL
    SELECT '3ème', 'EPS', 1 UNION ALL
    SELECT '2nde S', 'Français', 3 UNION ALL
    SELECT '2nde S', 'Mathématiques', 4 UNION ALL
    SELECT '2nde S', 'Physique-Chimie', 3 UNION ALL
    SELECT '2nde S', 'SVT', 3 UNION ALL
    SELECT '2nde S', 'Histoire-Géographie', 2 UNION ALL
    SELECT '2nde S', 'Anglais', 2 UNION ALL
    SELECT '2nde S', 'EPS', 1 UNION ALL
    SELECT '2nde A4', 'Français', 4 UNION ALL
    SELECT '2nde A4', 'Mathématiques', 3 UNION ALL
    SELECT '2nde A4', 'Histoire-Géographie', 3 UNION ALL
    SELECT '2nde A4', 'Anglais', 2 UNION ALL
    SELECT '2nde A4', 'Espagnol', 2 UNION ALL
    SELECT '2nde A4', 'EPS', 1 UNION ALL
    SELECT '1ère D', 'Français', 3 UNION ALL
    SELECT '1ère D', 'Mathématiques', 5 UNION ALL
    SELECT '1ère D', 'Physique-Chimie', 4 UNION ALL
    SELECT '1ère D', 'SVT', 4 UNION ALL
    SELECT '1ère D', 'Philosophie', 2 UNION ALL
    SELECT '1ère D', 'Anglais', 2 UNION ALL
    SELECT '1ère D', 'EPS', 1 UNION ALL
    SELECT '1ère A4', 'Français', 4 UNION ALL
    SELECT '1ère A4', 'Mathématiques', 3 UNION ALL
    SELECT '1ère A4', 'Philosophie', 3 UNION ALL
    SELECT '1ère A4', 'Histoire-Géographie', 3 UNION ALL
    SELECT '1ère A4', 'Anglais', 2 UNION ALL
    SELECT '1ère A4', 'EPS', 1 UNION ALL
    SELECT 'Terminale D', 'Français', 3 UNION ALL
    SELECT 'Terminale D', 'Mathématiques', 5 UNION ALL
    SELECT 'Terminale D', 'Physique-Chimie', 5 UNION ALL
    SELECT 'Terminale D', 'SVT', 5 UNION ALL
    SELECT 'Terminale D', 'Philosophie', 3 UNION ALL
    SELECT 'Terminale D', 'Anglais', 2 UNION ALL
    SELECT 'Terminale D', 'EPS', 1 UNION ALL
    SELECT 'Terminale A4', 'Français', 5 UNION ALL
    SELECT 'Terminale A4', 'Mathématiques', 3 UNION ALL
    SELECT 'Terminale A4', 'Philosophie', 4 UNION ALL
    SELECT 'Terminale A4', 'Histoire-Géographie', 4 UNION ALL
    SELECT 'Terminale A4', 'Anglais', 2 UNION ALL
    SELECT 'Terminale A4', 'EPS', 1
) cm ON cm.nv = n.nom
JOIN `matieres` m ON m.nom = cm.mat;

SET FOREIGN_KEY_CHECKS = 1;

SELECT 'Migration 006 : niveaux collège/lycée appliquée.' AS message;
