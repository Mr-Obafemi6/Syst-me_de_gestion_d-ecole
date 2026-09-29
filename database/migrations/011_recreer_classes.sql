-- Migration 011 : recrée les 10 classes (une par niveau) si elles n'existent pas.
-- Ne touche ni niveaux, ni matieres, ni classe_matieres — sûr à rejouer.

SET NAMES utf8mb4;

INSERT INTO `classes` (`niveau_id`, `nom`, `niveau`, `annee_scolaire_id`, `effectif_maximum`, `statut`)
SELECT n.id, 'A', n.nom, a.id, 40, 1
FROM `niveaux` n, (
    SELECT id FROM `annees_scolaires` WHERE active = 1 LIMIT 1
) a
WHERE NOT EXISTS (
    SELECT 1 FROM `classes` c
    WHERE c.niveau_id = n.id AND c.annee_scolaire_id = a.id
);

SELECT 'Migration 011 : classes recréées si besoin.' AS message;
SELECT n.nom AS niveau, c.id AS classe_id, c.nom AS classe_nom
FROM `classes` c
JOIN `niveaux` n ON n.id = c.niveau_id
ORDER BY n.ordre;
