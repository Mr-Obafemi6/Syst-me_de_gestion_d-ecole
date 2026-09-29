-- Migration 012 : corrige la contrainte d'unicité de `classes` (elle ignorait le niveau)
-- puis recrée les 10 classes manquantes.
-- Non destructif pour le reste (niveaux, matieres, classe_matieres intacts).

SET NAMES utf8mb4;

-- 1) Supprimer l'ancienne contrainte, trop large : (annee_scolaire_id, nom)
--    empêchait d'avoir deux classes nommées 'A' la même année, même dans
--    des niveaux différents.
SET @has_old_key = (
    SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'classes'
      AND INDEX_NAME = 'uk_classe_annee_nom'
);
SET @sql = IF(@has_old_key > 0,
    'ALTER TABLE `classes` DROP INDEX `uk_classe_annee_nom`',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2) Nouvelle contrainte correcte : (annee_scolaire_id, niveau_id, nom)
--    Une classe 'A' par niveau et par année est permise, mais pas deux fois
--    la même (niveau, nom) la même année.
SET @has_new_key = (
    SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'classes'
      AND INDEX_NAME = 'uk_classe_annee_niveau_nom'
);
SET @sql = IF(@has_new_key = 0,
    'ALTER TABLE `classes` ADD UNIQUE KEY `uk_classe_annee_niveau_nom` (`annee_scolaire_id`, `niveau_id`, `nom`)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 3) Recréer les 10 classes (une par niveau), maintenant que la contrainte le permet.
INSERT INTO `classes` (`niveau_id`, `nom`, `niveau`, `annee_scolaire_id`, `effectif_maximum`, `statut`)
SELECT n.id, 'A', n.nom, a.id, 40, 1
FROM `niveaux` n, (
    SELECT id FROM `annees_scolaires` WHERE active = 1 LIMIT 1
) a
WHERE NOT EXISTS (
    SELECT 1 FROM `classes` c
    WHERE c.niveau_id = n.id AND c.annee_scolaire_id = a.id
);

SELECT 'Migration 012 : contrainte corrigée, classes recréées.' AS message;
SELECT n.nom AS niveau, c.id AS classe_id, c.nom AS classe_nom
FROM `classes` c
JOIN `niveaux` n ON n.id = c.niveau_id
ORDER BY n.ordre;
