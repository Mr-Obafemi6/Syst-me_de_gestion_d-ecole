-- Migration 009 : matières pour 2nde S, 1ère A4 et Terminale A4 (sans coefficient)
-- Les classes (créées en migration 006) et les matières existent déjà.
-- Ici on active seulement l'association classe/matière pour ces 3 niveaux,
-- restés volontairement sans coefficient en migration 007 faute de grille
-- officielle confirmée. Coefficient laissé à NULL — à compléter dès que
-- la grille officielle est disponible (voir migration 010 à venir).
--
-- Liste de matières calquée sur le niveau parallèle déjà confirmé :
--   2nde S       ~ mêmes matières que 2nde A4 (ordre 6)
--   1ère A4      ~ mêmes matières que 1ère D   (ordre 7)
--   Terminale A4 ~ mêmes matières que Terminale D (ordre 9)
-- Non destructif : n'écrase aucun coefficient déjà renseigné.

SET NAMES utf8mb4;

INSERT INTO `classe_matieres` (`classe_id`, `matiere_id`, `coefficient`, `volume_horaire`, `annee_scolaire_id`, `statut`)
SELECT c.id, m.id, NULL, NULL, c.annee_scolaire_id, 1
FROM (
    -- 2nde S (ordre 5) : mêmes matières que 2nde A4
    SELECT 5 AS niveau_ordre, 'FR' AS code UNION ALL SELECT 5, 'ANG' UNION ALL SELECT 5, 'HG' UNION ALL
    SELECT 5, 'MATH' UNION ALL SELECT 5, 'PHIL' UNION ALL SELECT 5, 'SVT' UNION ALL SELECT 5, 'SP' UNION ALL
    SELECT 5, 'EC' UNION ALL SELECT 5, 'EPS' UNION ALL SELECT 5, 'INFO' UNION ALL

    -- 1ère A4 (ordre 8) : mêmes matières que 1ère D
    SELECT 8, 'FR' UNION ALL SELECT 8, 'ANG' UNION ALL SELECT 8, 'HG' UNION ALL
    SELECT 8, 'MATH' UNION ALL SELECT 8, 'PHIL' UNION ALL SELECT 8, 'SVT' UNION ALL SELECT 8, 'SP' UNION ALL
    SELECT 8, 'EC' UNION ALL SELECT 8, 'EPS' UNION ALL SELECT 8, 'INFO' UNION ALL

    -- Terminale A4 (ordre 10) : mêmes matières que Terminale D
    SELECT 10, 'FR' UNION ALL SELECT 10, 'ANG' UNION ALL SELECT 10, 'HG' UNION ALL
    SELECT 10, 'MATH' UNION ALL SELECT 10, 'PHIL' UNION ALL SELECT 10, 'SVT' UNION ALL SELECT 10, 'SP' UNION ALL
    SELECT 10, 'EPS' UNION ALL SELECT 10, 'INFO'
) x
JOIN `niveaux` n ON n.ordre = x.niveau_ordre
JOIN `classes` c ON c.niveau_id = n.id
JOIN `matieres` m ON m.code = x.code
WHERE c.statut = 1
ON DUPLICATE KEY UPDATE
    volume_horaire = NULL,
    statut = 1;
    -- coefficient volontairement absent du UPDATE : ne touche pas une valeur
    -- déjà renseignée si cette migration est rejouée après coup.

SELECT 'Migration 009 : matières 2nde S / 1ère A4 / Terminale A4 associées (coefficient à compléter).' AS message;
