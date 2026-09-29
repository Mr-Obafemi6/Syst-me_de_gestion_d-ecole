-- Migration 010 : élèves de test (données fictives) — 6ème à Terminale A4, séries D et A4
-- Effectif : entre 50 et 75 élèves par classe, réparti aléatoirement.
-- Noms, prénoms et dates de naissance sont FICTIFS, générés pour peupler l'environnement de test.
SET NAMES utf8mb4;

-- ===== 6ème : 70 élèves =====
INSERT INTO `eleves` (`matricule`, `nom`, `prenom`, `date_naissance`, `sexe`, `classe_id`, `actif`)
SELECT s.matricule, s.nom, s.prenom, s.date_naissance, s.sexe, c.id, 1
FROM (
    SELECT 'SGE2025-0001' AS matricule, 'Bayaki' AS nom, 'Kodjo' AS prenom, '2013-04-08' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0002' AS matricule, 'Batchana' AS nom, 'Sena' AS prenom, '2012-10-14' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0003' AS matricule, 'Adjei' AS nom, 'Kodjo' AS prenom, '2012-04-17' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0004' AS matricule, 'Toundou' AS nom, 'Foli' AS prenom, '2013-04-15' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0005' AS matricule, 'Amegan' AS nom, 'Kokoévi' AS prenom, '2012-12-14' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0006' AS matricule, 'Bassowa' AS nom, 'Enyonam' AS prenom, '2012-06-04' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0007' AS matricule, 'Ayikoue' AS nom, 'Dogbevi' AS prenom, '2013-06-20' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0008' AS matricule, 'Agbeko' AS nom, 'Kokoévi' AS prenom, '2013-09-04' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0009' AS matricule, 'Gbati' AS nom, 'Akossiwa' AS prenom, '2013-11-20' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0010' AS matricule, 'Sossou' AS nom, 'Adjovi' AS prenom, '2012-01-22' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0011' AS matricule, 'Adjei' AS nom, 'Essobiyou' AS prenom, '2012-02-13' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0012' AS matricule, 'Padabo' AS nom, 'Mawusi' AS prenom, '2013-03-12' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0013' AS matricule, 'Essowe' AS nom, 'Abra' AS prenom, '2013-12-22' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0014' AS matricule, 'Nyavor' AS nom, 'Elom' AS prenom, '2012-03-15' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0015' AS matricule, 'Padabo' AS nom, 'Enyonam' AS prenom, '2012-11-11' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0016' AS matricule, 'Agbeko' AS nom, 'Yawo' AS prenom, '2013-07-09' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0017' AS matricule, 'Douti' AS nom, 'Mawuli' AS prenom, '2013-04-21' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0018' AS matricule, 'Kondaani' AS nom, 'Delali' AS prenom, '2013-03-09' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0019' AS matricule, 'Bayaki' AS nom, 'Kofi' AS prenom, '2013-12-19' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0020' AS matricule, 'Wonyu' AS nom, 'Adjowa' AS prenom, '2013-06-08' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0021' AS matricule, 'Tossou' AS nom, 'Anani' AS prenom, '2012-01-28' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0022' AS matricule, 'Padabo' AS nom, 'Selom' AS prenom, '2012-11-14' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0023' AS matricule, 'Mensah' AS nom, 'Dogbevi' AS prenom, '2013-09-09' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0024' AS matricule, 'Batchana' AS nom, 'Enam' AS prenom, '2013-11-11' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0025' AS matricule, 'Tetteh' AS nom, 'Essobiyou' AS prenom, '2012-08-01' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0026' AS matricule, 'Baguilim' AS nom, 'Akpedje' AS prenom, '2012-09-04' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0027' AS matricule, 'Padabo' AS nom, 'Amavi' AS prenom, '2012-03-12' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0028' AS matricule, 'Amegan' AS nom, 'Setodji' AS prenom, '2013-08-01' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0029' AS matricule, 'Tchalla' AS nom, 'Mensah' AS prenom, '2012-01-08' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0030' AS matricule, 'Palanga' AS nom, 'Kwami' AS prenom, '2013-02-25' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0031' AS matricule, 'Essowe' AS nom, 'Edem' AS prenom, '2013-09-06' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0032' AS matricule, 'Adom' AS nom, 'Akpedje' AS prenom, '2013-04-18' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0033' AS matricule, 'Dogbevi' AS nom, 'Essohanam' AS prenom, '2013-08-17' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0034' AS matricule, 'Dogbe' AS nom, 'Adjoa' AS prenom, '2012-02-11' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0035' AS matricule, 'Wonyu' AS nom, 'Yawo' AS prenom, '2012-01-03' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0036' AS matricule, 'Komlan' AS nom, 'Yawo' AS prenom, '2012-06-03' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0037' AS matricule, 'Essowe' AS nom, 'Kudzo' AS prenom, '2013-04-18' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0038' AS matricule, 'Dogbe' AS nom, 'Amevi' AS prenom, '2013-07-07' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0039' AS matricule, 'Essowe' AS nom, 'Sena' AS prenom, '2013-06-14' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0040' AS matricule, 'Palanga' AS nom, 'Mawusi' AS prenom, '2012-11-21' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0041' AS matricule, 'Dogbevi' AS nom, 'Kokou' AS prenom, '2013-02-08' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0042' AS matricule, 'Nyavor' AS nom, 'Foli' AS prenom, '2013-03-14' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0043' AS matricule, 'Gbedjeha' AS nom, 'Kudzo' AS prenom, '2012-02-15' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0044' AS matricule, 'Kondaani' AS nom, 'Kokou' AS prenom, '2012-02-25' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0045' AS matricule, 'Agbenoto' AS nom, 'Elom' AS prenom, '2013-08-07' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0046' AS matricule, 'Kodjo' AS nom, 'Adjowa' AS prenom, '2012-07-01' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0047' AS matricule, 'Gbedjeha' AS nom, 'Enyonam' AS prenom, '2013-07-23' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0048' AS matricule, 'Sossou' AS nom, 'Akouavi' AS prenom, '2013-04-02' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0049' AS matricule, 'Kodjo' AS nom, 'Bawa' AS prenom, '2012-10-16' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0050' AS matricule, 'Attipoe' AS nom, 'Kokou' AS prenom, '2012-03-03' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0051' AS matricule, 'Dogbevi' AS nom, 'Kofi' AS prenom, '2012-10-08' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0052' AS matricule, 'Agbenoto' AS nom, 'Kwami' AS prenom, '2013-05-07' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0053' AS matricule, 'Awesso' AS nom, 'Akpene' AS prenom, '2013-03-22' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0054' AS matricule, 'Kolani' AS nom, 'Mawusi' AS prenom, '2012-01-15' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0055' AS matricule, 'Nyavor' AS nom, 'Yao' AS prenom, '2012-09-09' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0056' AS matricule, 'Komlan' AS nom, 'Alfa' AS prenom, '2012-06-10' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0057' AS matricule, 'Nyavor' AS nom, 'Fiifi' AS prenom, '2013-10-26' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0058' AS matricule, 'Essowe' AS nom, 'Essohanam' AS prenom, '2012-03-09' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0059' AS matricule, 'Bayaki' AS nom, 'Sena' AS prenom, '2012-05-10' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0060' AS matricule, 'Klutse' AS nom, 'Tchaa' AS prenom, '2013-09-16' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0061' AS matricule, 'Kodjo' AS nom, 'Adjowa' AS prenom, '2012-11-14' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0062' AS matricule, 'Amegan' AS nom, 'Afi' AS prenom, '2013-03-21' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0063' AS matricule, 'Bayaki' AS nom, 'Ablavi' AS prenom, '2013-09-23' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0064' AS matricule, 'Amegan' AS nom, 'Afiwa' AS prenom, '2012-02-23' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0065' AS matricule, 'Alassani' AS nom, 'Kossi' AS prenom, '2012-07-05' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0066' AS matricule, 'Alassani' AS nom, 'Essohanam' AS prenom, '2012-06-07' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0067' AS matricule, 'Ouro-Djeri' AS nom, 'Sena' AS prenom, '2013-10-24' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0068' AS matricule, 'Bakai' AS nom, 'Kofi' AS prenom, '2012-07-01' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0069' AS matricule, 'Agbenoto' AS nom, 'Tchaa' AS prenom, '2012-05-06' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0070' AS matricule, 'Agbeko' AS nom, 'Dogbevi' AS prenom, '2013-04-07' AS date_naissance, 'M' AS sexe
) s, (
    SELECT cl.id
    FROM `classes` cl
    JOIN `niveaux` nv ON nv.id = cl.niveau_id
    JOIN `annees_scolaires` an ON an.id = cl.annee_scolaire_id
    WHERE nv.nom = '6ème' AND an.active = 1
    LIMIT 1
) c
ON DUPLICATE KEY UPDATE `matricule` = VALUES(`matricule`);

-- ===== 5ème : 64 élèves =====
INSERT INTO `eleves` (`matricule`, `nom`, `prenom`, `date_naissance`, `sexe`, `classe_id`, `actif`)
SELECT s.matricule, s.nom, s.prenom, s.date_naissance, s.sexe, c.id, 1
FROM (
    SELECT 'SGE2025-0071' AS matricule, 'Amenyo' AS nom, 'Efua' AS prenom, '2011-01-22' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0072' AS matricule, 'Bissa' AS nom, 'Kossivi' AS prenom, '2012-02-25' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0073' AS matricule, 'Kondaani' AS nom, 'Yawa' AS prenom, '2012-11-27' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0074' AS matricule, 'Djondo' AS nom, 'Ama' AS prenom, '2012-03-19' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0075' AS matricule, 'Ayikoue' AS nom, 'Afi' AS prenom, '2012-06-24' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0076' AS matricule, 'Adom' AS nom, 'Elikem' AS prenom, '2011-07-19' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0077' AS matricule, 'Agbeko' AS nom, 'Sitsofe' AS prenom, '2012-01-17' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0078' AS matricule, 'Tetteh' AS nom, 'Mensah' AS prenom, '2011-11-11' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0079' AS matricule, 'Djondo' AS nom, 'Kayi' AS prenom, '2012-09-10' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0080' AS matricule, 'Dogbevi' AS nom, 'Selorm' AS prenom, '2012-09-05' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0081' AS matricule, 'Essowe' AS nom, 'Komivi' AS prenom, '2012-11-24' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0082' AS matricule, 'Dogbevi' AS nom, 'Essohanam' AS prenom, '2011-05-10' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0083' AS matricule, 'Wonyu' AS nom, 'Sedem' AS prenom, '2012-08-15' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0084' AS matricule, 'Klutse' AS nom, 'Kayi' AS prenom, '2012-12-06' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0085' AS matricule, 'Attipoe' AS nom, 'Essobiyou' AS prenom, '2012-02-27' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0086' AS matricule, 'Amenyo' AS nom, 'Essohanam' AS prenom, '2011-03-01' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0087' AS matricule, 'Kpodar' AS nom, 'Kofi' AS prenom, '2011-08-14' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0088' AS matricule, 'Tossou' AS nom, 'Dogbevi' AS prenom, '2012-04-05' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0089' AS matricule, 'Tetteh' AS nom, 'Sena' AS prenom, '2011-03-26' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0090' AS matricule, 'Gbati' AS nom, 'Afi' AS prenom, '2011-02-15' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0091' AS matricule, 'Essowe' AS nom, 'Delali' AS prenom, '2012-08-20' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0092' AS matricule, 'Gbati' AS nom, 'Amavi' AS prenom, '2012-03-24' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0093' AS matricule, 'Awesso' AS nom, 'Mawusi' AS prenom, '2011-11-09' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0094' AS matricule, 'Dogbe' AS nom, 'Edoh' AS prenom, '2012-08-03' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0095' AS matricule, 'Sambiani' AS nom, 'Akpene' AS prenom, '2012-06-18' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0096' AS matricule, 'Bassowa' AS nom, 'Edem' AS prenom, '2011-07-23' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0097' AS matricule, 'Komlan' AS nom, 'Mawuli' AS prenom, '2012-07-11' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0098' AS matricule, 'Kodjo' AS nom, 'Elikem' AS prenom, '2011-07-13' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0099' AS matricule, 'Kpodar' AS nom, 'Dogbevi' AS prenom, '2011-06-10' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0100' AS matricule, 'Agbenoto' AS nom, 'Sitso' AS prenom, '2011-08-08' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0101' AS matricule, 'Tossou' AS nom, 'Elikem' AS prenom, '2011-07-11' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0102' AS matricule, 'Bakai' AS nom, 'Essowavana' AS prenom, '2012-03-20' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0103' AS matricule, 'Wonyu' AS nom, 'Kossivi' AS prenom, '2011-02-21' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0104' AS matricule, 'Gbedjeha' AS nom, 'Akouavi' AS prenom, '2011-01-09' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0105' AS matricule, 'Klutse' AS nom, 'Selorm' AS prenom, '2012-06-11' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0106' AS matricule, 'Baguilim' AS nom, 'Enyonam' AS prenom, '2012-05-27' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0107' AS matricule, 'Adjovi' AS nom, 'Amevi' AS prenom, '2011-06-08' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0108' AS matricule, 'Baguilim' AS nom, 'Kossi' AS prenom, '2011-04-07' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0109' AS matricule, 'Dogbe' AS nom, 'Selom' AS prenom, '2011-08-22' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0110' AS matricule, 'Gbedjeha' AS nom, 'Mawuli' AS prenom, '2012-06-06' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0111' AS matricule, 'Tchalla' AS nom, 'Elom' AS prenom, '2011-10-01' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0112' AS matricule, 'Batchana' AS nom, 'Adjovi' AS prenom, '2012-07-23' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0113' AS matricule, 'Wonyu' AS nom, 'Yao' AS prenom, '2011-02-23' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0114' AS matricule, 'Batchana' AS nom, 'Sitso' AS prenom, '2011-10-26' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0115' AS matricule, 'Nyavor' AS nom, 'Alfa' AS prenom, '2012-11-12' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0116' AS matricule, 'Kondaani' AS nom, 'Anani' AS prenom, '2012-01-28' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0117' AS matricule, 'Tossou' AS nom, 'Amavi' AS prenom, '2011-07-12' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0118' AS matricule, 'Bassowa' AS nom, 'Nadège' AS prenom, '2012-03-24' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0119' AS matricule, 'Nyavor' AS nom, 'Akoua' AS prenom, '2012-08-14' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0120' AS matricule, 'Dogbe' AS nom, 'Selorm' AS prenom, '2011-05-15' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0121' AS matricule, 'Douti' AS nom, 'Delali' AS prenom, '2012-06-01' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0122' AS matricule, 'Kolani' AS nom, 'Sitso' AS prenom, '2011-08-07' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0123' AS matricule, 'Awesso' AS nom, 'Kokoévi' AS prenom, '2012-05-20' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0124' AS matricule, 'Amegan' AS nom, 'Afiwa' AS prenom, '2011-02-08' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0125' AS matricule, 'Gbati' AS nom, 'Akosua' AS prenom, '2011-12-16' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0126' AS matricule, 'Adjovi' AS nom, 'Mawusi' AS prenom, '2011-05-08' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0127' AS matricule, 'Dogbe' AS nom, 'Nadège' AS prenom, '2012-11-19' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0128' AS matricule, 'Gbati' AS nom, 'Akosua' AS prenom, '2012-07-24' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0129' AS matricule, 'Kolani' AS nom, 'Yawa' AS prenom, '2012-05-10' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0130' AS matricule, 'Djondo' AS nom, 'Akpene' AS prenom, '2011-06-04' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0131' AS matricule, 'Klutse' AS nom, 'Foli' AS prenom, '2012-05-24' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0132' AS matricule, 'Sossou' AS nom, 'Adjoa' AS prenom, '2012-04-12' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0133' AS matricule, 'Amegan' AS nom, 'Essohanam' AS prenom, '2011-05-02' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0134' AS matricule, 'Kolani' AS nom, 'Essobiyou' AS prenom, '2011-11-28' AS date_naissance, 'M' AS sexe
) s, (
    SELECT cl.id
    FROM `classes` cl
    JOIN `niveaux` nv ON nv.id = cl.niveau_id
    JOIN `annees_scolaires` an ON an.id = cl.annee_scolaire_id
    WHERE nv.nom = '5ème' AND an.active = 1
    LIMIT 1
) c
ON DUPLICATE KEY UPDATE `matricule` = VALUES(`matricule`);

-- ===== 4ème : 74 élèves =====
INSERT INTO `eleves` (`matricule`, `nom`, `prenom`, `date_naissance`, `sexe`, `classe_id`, `actif`)
SELECT s.matricule, s.nom, s.prenom, s.date_naissance, s.sexe, c.id, 1
FROM (
    SELECT 'SGE2025-0135' AS matricule, 'Amegan' AS nom, 'Adjoa' AS prenom, '2011-08-16' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0136' AS matricule, 'Amétépé' AS nom, 'Selorm' AS prenom, '2010-05-28' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0137' AS matricule, 'Komlan' AS nom, 'Adjoa' AS prenom, '2011-08-03' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0138' AS matricule, 'Bassowa' AS nom, 'Selom' AS prenom, '2011-02-08' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0139' AS matricule, 'Adom' AS nom, 'Komivi' AS prenom, '2010-09-13' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0140' AS matricule, 'Ayeva' AS nom, 'Bertine' AS prenom, '2011-10-14' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0141' AS matricule, 'Yendoubouame' AS nom, 'Adjovi' AS prenom, '2010-10-24' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0142' AS matricule, 'Padabo' AS nom, 'Mawuli' AS prenom, '2010-05-22' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0143' AS matricule, 'Dogbe' AS nom, 'Elom' AS prenom, '2010-09-03' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0144' AS matricule, 'Agbenoto' AS nom, 'Komlan' AS prenom, '2011-12-20' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0145' AS matricule, 'Agbeko' AS nom, 'Efua' AS prenom, '2010-05-23' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0146' AS matricule, 'Gbedjeha' AS nom, 'Nadège' AS prenom, '2010-11-08' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0147' AS matricule, 'Padabo' AS nom, 'Kokoévi' AS prenom, '2010-07-04' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0148' AS matricule, 'Sambiani' AS nom, 'Selom' AS prenom, '2010-02-02' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0149' AS matricule, 'Adom' AS nom, 'Essohanam' AS prenom, '2011-08-04' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0150' AS matricule, 'Tchalla' AS nom, 'Nadège' AS prenom, '2011-05-17' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0151' AS matricule, 'Adjei' AS nom, 'Mawusi' AS prenom, '2010-07-24' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0152' AS matricule, 'Awesso' AS nom, 'Akoua' AS prenom, '2010-02-08' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0153' AS matricule, 'Douti' AS nom, 'Kudzo' AS prenom, '2010-03-16' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0154' AS matricule, 'Sambiani' AS nom, 'Bertine' AS prenom, '2010-10-14' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0155' AS matricule, 'Kpodar' AS nom, 'Akossiwa' AS prenom, '2011-07-11' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0156' AS matricule, 'Ayikoue' AS nom, 'Kayi' AS prenom, '2010-06-14' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0157' AS matricule, 'Essowe' AS nom, 'Efua' AS prenom, '2011-09-02' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0158' AS matricule, 'Kolani' AS nom, 'Akossiwa' AS prenom, '2011-06-04' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0159' AS matricule, 'Attipoe' AS nom, 'Sitso' AS prenom, '2010-11-28' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0160' AS matricule, 'Kodjo' AS nom, 'Elikem' AS prenom, '2010-09-12' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0161' AS matricule, 'Ayeva' AS nom, 'Edoh' AS prenom, '2010-04-09' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0162' AS matricule, 'Ayeva' AS nom, 'Essobiyou' AS prenom, '2011-02-01' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0163' AS matricule, 'Tchalla' AS nom, 'Elom' AS prenom, '2010-09-14' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0164' AS matricule, 'Djondo' AS nom, 'Yawo' AS prenom, '2011-02-21' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0165' AS matricule, 'Toundou' AS nom, 'Fofo' AS prenom, '2011-09-23' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0166' AS matricule, 'Kpodar' AS nom, 'Elikem' AS prenom, '2011-04-15' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0167' AS matricule, 'Sossou' AS nom, 'Dogbevi' AS prenom, '2010-02-09' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0168' AS matricule, 'Attipoe' AS nom, 'Selorm' AS prenom, '2011-01-10' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0169' AS matricule, 'Wonyu' AS nom, 'Amavi' AS prenom, '2011-03-15' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0170' AS matricule, 'Bissa' AS nom, 'Yawa' AS prenom, '2011-08-11' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0171' AS matricule, 'Douti' AS nom, 'Kofi' AS prenom, '2011-04-28' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0172' AS matricule, 'Kolani' AS nom, 'Afi' AS prenom, '2011-12-26' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0173' AS matricule, 'Essowe' AS nom, 'Delali' AS prenom, '2010-08-02' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0174' AS matricule, 'Wonyu' AS nom, 'Anani' AS prenom, '2011-02-28' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0175' AS matricule, 'Amewuho' AS nom, 'Adjoa' AS prenom, '2011-01-24' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0176' AS matricule, 'Kondaani' AS nom, 'Komivi' AS prenom, '2010-02-16' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0177' AS matricule, 'Yendoubouame' AS nom, 'Selorm' AS prenom, '2011-11-03' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0178' AS matricule, 'Batchana' AS nom, 'Sitso' AS prenom, '2011-06-21' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0179' AS matricule, 'Nyavor' AS nom, 'Sitso' AS prenom, '2010-10-03' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0180' AS matricule, 'Amenyo' AS nom, 'Essobiyou' AS prenom, '2010-07-04' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0181' AS matricule, 'Bakai' AS nom, 'Fiifi' AS prenom, '2011-01-02' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0182' AS matricule, 'Kodjo' AS nom, 'Kokoévi' AS prenom, '2011-06-12' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0183' AS matricule, 'Dogbe' AS nom, 'Akouavi' AS prenom, '2011-10-22' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0184' AS matricule, 'Amétépé' AS nom, 'Elom' AS prenom, '2010-10-28' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0185' AS matricule, 'Batchana' AS nom, 'Akoua' AS prenom, '2010-08-19' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0186' AS matricule, 'Gbedjeha' AS nom, 'Yawo' AS prenom, '2011-08-09' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0187' AS matricule, 'Poutouli' AS nom, 'Delali' AS prenom, '2010-02-15' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0188' AS matricule, 'Tchalla' AS nom, 'Adjovi' AS prenom, '2011-12-09' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0189' AS matricule, 'Tchalla' AS nom, 'Sitso' AS prenom, '2010-07-28' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0190' AS matricule, 'Dogbe' AS nom, 'Adjoa' AS prenom, '2011-10-12' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0191' AS matricule, 'Poutouli' AS nom, 'Nadège' AS prenom, '2010-11-13' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0192' AS matricule, 'Douti' AS nom, 'Ama' AS prenom, '2010-10-24' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0193' AS matricule, 'Poutouli' AS nom, 'Amavi' AS prenom, '2010-10-26' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0194' AS matricule, 'Padabo' AS nom, 'Akpene' AS prenom, '2010-10-09' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0195' AS matricule, 'Padabo' AS nom, 'Sena' AS prenom, '2010-05-26' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0196' AS matricule, 'Wonyu' AS nom, 'Afi' AS prenom, '2011-12-05' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0197' AS matricule, 'Kolani' AS nom, 'Essobiyou' AS prenom, '2011-03-07' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0198' AS matricule, 'Amewuho' AS nom, 'Mensah' AS prenom, '2011-03-09' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0199' AS matricule, 'Poutouli' AS nom, 'Kokoévi' AS prenom, '2011-02-15' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0200' AS matricule, 'Baguilim' AS nom, 'Selom' AS prenom, '2010-11-24' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0201' AS matricule, 'Gbati' AS nom, 'Sitso' AS prenom, '2011-02-26' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0202' AS matricule, 'Awesso' AS nom, 'Ama' AS prenom, '2010-08-12' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0203' AS matricule, 'Mensah' AS nom, 'Adjovi' AS prenom, '2011-02-22' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0204' AS matricule, 'Adjovi' AS nom, 'Amevi' AS prenom, '2011-10-08' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0205' AS matricule, 'Kolani' AS nom, 'Delali' AS prenom, '2011-11-14' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0206' AS matricule, 'Agbeko' AS nom, 'Edem' AS prenom, '2010-05-16' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0207' AS matricule, 'Dogbe' AS nom, 'Sena' AS prenom, '2010-07-15' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0208' AS matricule, 'Bayaki' AS nom, 'Kayi' AS prenom, '2011-10-24' AS date_naissance, 'F' AS sexe
) s, (
    SELECT cl.id
    FROM `classes` cl
    JOIN `niveaux` nv ON nv.id = cl.niveau_id
    JOIN `annees_scolaires` an ON an.id = cl.annee_scolaire_id
    WHERE nv.nom = '4ème' AND an.active = 1
    LIMIT 1
) c
ON DUPLICATE KEY UPDATE `matricule` = VALUES(`matricule`);

-- ===== 3ème : 73 élèves =====
INSERT INTO `eleves` (`matricule`, `nom`, `prenom`, `date_naissance`, `sexe`, `classe_id`, `actif`)
SELECT s.matricule, s.nom, s.prenom, s.date_naissance, s.sexe, c.id, 1
FROM (
    SELECT 'SGE2025-0209' AS matricule, 'Kondaani' AS nom, 'Komivi' AS prenom, '2009-08-20' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0210' AS matricule, 'Agbeko' AS nom, 'Enyonam' AS prenom, '2010-04-15' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0211' AS matricule, 'Alassani' AS nom, 'Akpene' AS prenom, '2009-11-12' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0212' AS matricule, 'Dogbevi' AS nom, 'Afi' AS prenom, '2010-04-04' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0213' AS matricule, 'Essowe' AS nom, 'Akossiwa' AS prenom, '2009-11-21' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0214' AS matricule, 'Bissa' AS nom, 'Kokou' AS prenom, '2009-03-26' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0215' AS matricule, 'Baguilim' AS nom, 'Yao' AS prenom, '2009-10-07' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0216' AS matricule, 'Bassowa' AS nom, 'Tchaa' AS prenom, '2009-05-28' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0217' AS matricule, 'Nyavor' AS nom, 'Edem' AS prenom, '2010-03-04' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0218' AS matricule, 'Amegan' AS nom, 'Edem' AS prenom, '2010-04-19' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0219' AS matricule, 'Amétépé' AS nom, 'Ama' AS prenom, '2010-01-05' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0220' AS matricule, 'Djondo' AS nom, 'Akpedje' AS prenom, '2009-08-15' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0221' AS matricule, 'Wonyu' AS nom, 'Akpedje' AS prenom, '2009-08-17' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0222' AS matricule, 'Palanga' AS nom, 'Kossi' AS prenom, '2010-08-21' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0223' AS matricule, 'Kpodar' AS nom, 'Kokou' AS prenom, '2010-07-22' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0224' AS matricule, 'Toundou' AS nom, 'Fofo' AS prenom, '2010-02-03' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0225' AS matricule, 'Bassowa' AS nom, 'Akoua' AS prenom, '2009-03-09' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0226' AS matricule, 'Adom' AS nom, 'Delali' AS prenom, '2010-08-17' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0227' AS matricule, 'Kolani' AS nom, 'Adjoa' AS prenom, '2009-11-21' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0228' AS matricule, 'Ayeva' AS nom, 'Sedem' AS prenom, '2009-07-11' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0229' AS matricule, 'Agbenoto' AS nom, 'Delali' AS prenom, '2009-06-14' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0230' AS matricule, 'Awesso' AS nom, 'Kayi' AS prenom, '2010-03-22' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0231' AS matricule, 'Adjei' AS nom, 'Akossiwa' AS prenom, '2009-02-14' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0232' AS matricule, 'Tchamie' AS nom, 'Mensah' AS prenom, '2009-10-18' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0233' AS matricule, 'Djondo' AS nom, 'Kayi' AS prenom, '2010-06-28' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0234' AS matricule, 'Palanga' AS nom, 'Sitso' AS prenom, '2009-05-20' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0235' AS matricule, 'Ayikoue' AS nom, 'Yawa' AS prenom, '2009-03-22' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0236' AS matricule, 'Ayikoue' AS nom, 'Akpene' AS prenom, '2010-09-12' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0237' AS matricule, 'Douti' AS nom, 'Kudzo' AS prenom, '2009-07-28' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0238' AS matricule, 'Adjovi' AS nom, 'Kudzo' AS prenom, '2009-05-23' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0239' AS matricule, 'Bissa' AS nom, 'Bertine' AS prenom, '2010-01-06' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0240' AS matricule, 'Komlan' AS nom, 'Kossivi' AS prenom, '2009-12-21' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0241' AS matricule, 'Bayaki' AS nom, 'Kwami' AS prenom, '2009-07-14' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0242' AS matricule, 'Bakai' AS nom, 'Selorm' AS prenom, '2010-05-24' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0243' AS matricule, 'Douti' AS nom, 'Rachida' AS prenom, '2009-01-05' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0244' AS matricule, 'Batchana' AS nom, 'Kokou' AS prenom, '2009-05-15' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0245' AS matricule, 'Adom' AS nom, 'Akosua' AS prenom, '2010-07-09' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0246' AS matricule, 'Djondo' AS nom, 'Anani' AS prenom, '2010-07-04' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0247' AS matricule, 'Batchana' AS nom, 'Kayi' AS prenom, '2010-09-22' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0248' AS matricule, 'Amenyo' AS nom, 'Afi' AS prenom, '2010-10-02' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0249' AS matricule, 'Tchalla' AS nom, 'Mawuli' AS prenom, '2009-03-25' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0250' AS matricule, 'Kolani' AS nom, 'Efua' AS prenom, '2009-01-16' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0251' AS matricule, 'Tchamie' AS nom, 'Ablavi' AS prenom, '2010-09-23' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0252' AS matricule, 'Gbati' AS nom, 'Anani' AS prenom, '2010-02-13' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0253' AS matricule, 'Adjovi' AS nom, 'Sedem' AS prenom, '2010-02-28' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0254' AS matricule, 'Tetteh' AS nom, 'Adjovi' AS prenom, '2010-12-21' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0255' AS matricule, 'Djondo' AS nom, 'Efua' AS prenom, '2010-01-11' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0256' AS matricule, 'Kolani' AS nom, 'Delali' AS prenom, '2010-02-14' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0257' AS matricule, 'Tetteh' AS nom, 'Kofi' AS prenom, '2010-09-03' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0258' AS matricule, 'Tchalla' AS nom, 'Sitso' AS prenom, '2010-04-11' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0259' AS matricule, 'Attipoe' AS nom, 'Yao' AS prenom, '2009-09-17' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0260' AS matricule, 'Ouro-Djeri' AS nom, 'Alfa' AS prenom, '2009-04-04' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0261' AS matricule, 'Sossou' AS nom, 'Sitsofe' AS prenom, '2009-10-05' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0262' AS matricule, 'Padabo' AS nom, 'Ayivi' AS prenom, '2010-08-25' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0263' AS matricule, 'Douti' AS nom, 'Kayi' AS prenom, '2010-11-11' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0264' AS matricule, 'Komlan' AS nom, 'Fiifi' AS prenom, '2010-08-21' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0265' AS matricule, 'Sambiani' AS nom, 'Kokoévi' AS prenom, '2009-06-17' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0266' AS matricule, 'Gbedjeha' AS nom, 'Essohanam' AS prenom, '2010-01-02' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0267' AS matricule, 'Poutouli' AS nom, 'Amavi' AS prenom, '2009-11-28' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0268' AS matricule, 'Mensah' AS nom, 'Anani' AS prenom, '2010-10-18' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0269' AS matricule, 'Douti' AS nom, 'Fiifi' AS prenom, '2009-06-20' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0270' AS matricule, 'Bassowa' AS nom, 'Akpedje' AS prenom, '2009-08-04' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0271' AS matricule, 'Adjei' AS nom, 'Nadège' AS prenom, '2009-01-08' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0272' AS matricule, 'Amewuho' AS nom, 'Mawusi' AS prenom, '2009-06-12' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0273' AS matricule, 'Agbenoto' AS nom, 'Delali' AS prenom, '2010-11-20' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0274' AS matricule, 'Komlan' AS nom, 'Tchaa' AS prenom, '2010-02-18' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0275' AS matricule, 'Awesso' AS nom, 'Efua' AS prenom, '2009-06-03' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0276' AS matricule, 'Tchalla' AS nom, 'Alfa' AS prenom, '2010-03-20' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0277' AS matricule, 'Gbati' AS nom, 'Essohanam' AS prenom, '2010-11-26' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0278' AS matricule, 'Tchamie' AS nom, 'Amavi' AS prenom, '2009-11-22' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0279' AS matricule, 'Alassani' AS nom, 'Akpedje' AS prenom, '2009-06-10' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0280' AS matricule, 'Bissa' AS nom, 'Mawuli' AS prenom, '2010-04-08' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0281' AS matricule, 'Komlan' AS nom, 'Selom' AS prenom, '2010-02-17' AS date_naissance, 'M' AS sexe
) s, (
    SELECT cl.id
    FROM `classes` cl
    JOIN `niveaux` nv ON nv.id = cl.niveau_id
    JOIN `annees_scolaires` an ON an.id = cl.annee_scolaire_id
    WHERE nv.nom = '3ème' AND an.active = 1
    LIMIT 1
) c
ON DUPLICATE KEY UPDATE `matricule` = VALUES(`matricule`);

-- ===== 2nde S : 74 élèves =====
INSERT INTO `eleves` (`matricule`, `nom`, `prenom`, `date_naissance`, `sexe`, `classe_id`, `actif`)
SELECT s.matricule, s.nom, s.prenom, s.date_naissance, s.sexe, c.id, 1
FROM (
    SELECT 'SGE2025-0282' AS matricule, 'Yendoubouame' AS nom, 'Tchaa' AS prenom, '2008-10-13' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0283' AS matricule, 'Amétépé' AS nom, 'Elom' AS prenom, '2008-12-15' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0284' AS matricule, 'Alassani' AS nom, 'Komivi' AS prenom, '2008-08-20' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0285' AS matricule, 'Bayaki' AS nom, 'Rachida' AS prenom, '2009-04-18' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0286' AS matricule, 'Kpodar' AS nom, 'Essohanam' AS prenom, '2008-06-22' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0287' AS matricule, 'Poutouli' AS nom, 'Mawusi' AS prenom, '2009-09-17' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0288' AS matricule, 'Sossou' AS nom, 'Ablavi' AS prenom, '2008-05-02' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0289' AS matricule, 'Alassani' AS nom, 'Sitso' AS prenom, '2008-12-28' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0290' AS matricule, 'Adjei' AS nom, 'Essobiyou' AS prenom, '2008-05-15' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0291' AS matricule, 'Adjei' AS nom, 'Sedem' AS prenom, '2008-08-12' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0292' AS matricule, 'Kodjo' AS nom, 'Komivi' AS prenom, '2009-09-12' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0293' AS matricule, 'Adjei' AS nom, 'Dogbevi' AS prenom, '2009-04-01' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0294' AS matricule, 'Ayikoue' AS nom, 'Bertine' AS prenom, '2009-03-05' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0295' AS matricule, 'Kpodar' AS nom, 'Essobiyou' AS prenom, '2008-12-16' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0296' AS matricule, 'Amegan' AS nom, 'Akoua' AS prenom, '2008-01-09' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0297' AS matricule, 'Gbati' AS nom, 'Selom' AS prenom, '2009-02-25' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0298' AS matricule, 'Tchalla' AS nom, 'Akpene' AS prenom, '2008-01-08' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0299' AS matricule, 'Yendoubouame' AS nom, 'Edoh' AS prenom, '2009-02-04' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0300' AS matricule, 'Nyavor' AS nom, 'Akoua' AS prenom, '2008-11-17' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0301' AS matricule, 'Poutouli' AS nom, 'Selom' AS prenom, '2009-01-20' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0302' AS matricule, 'Douti' AS nom, 'Akpene' AS prenom, '2009-03-22' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0303' AS matricule, 'Alassani' AS nom, 'Setodji' AS prenom, '2008-09-18' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0304' AS matricule, 'Kpodar' AS nom, 'Dogbevi' AS prenom, '2008-11-13' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0305' AS matricule, 'Bayaki' AS nom, 'Enyonam' AS prenom, '2008-06-26' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0306' AS matricule, 'Dogbe' AS nom, 'Alfa' AS prenom, '2008-10-24' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0307' AS matricule, 'Agbeko' AS nom, 'Akouavi' AS prenom, '2009-09-11' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0308' AS matricule, 'Kolani' AS nom, 'Delali' AS prenom, '2009-11-06' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0309' AS matricule, 'Toundou' AS nom, 'Yao' AS prenom, '2009-01-10' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0310' AS matricule, 'Sossou' AS nom, 'Kossi' AS prenom, '2008-06-10' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0311' AS matricule, 'Nyavor' AS nom, 'Amavi' AS prenom, '2009-05-02' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0312' AS matricule, 'Ouro-Djeri' AS nom, 'Essobiyou' AS prenom, '2008-11-11' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0313' AS matricule, 'Alassani' AS nom, 'Adjoa' AS prenom, '2009-07-24' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0314' AS matricule, 'Mensah' AS nom, 'Adjowa' AS prenom, '2009-03-16' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0315' AS matricule, 'Amewuho' AS nom, 'Yawa' AS prenom, '2009-02-24' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0316' AS matricule, 'Tetteh' AS nom, 'Akossiwa' AS prenom, '2008-09-10' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0317' AS matricule, 'Adjei' AS nom, 'Adjoa' AS prenom, '2009-11-10' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0318' AS matricule, 'Adom' AS nom, 'Mawusi' AS prenom, '2009-03-23' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0319' AS matricule, 'Ayeva' AS nom, 'Yawa' AS prenom, '2008-12-28' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0320' AS matricule, 'Tetteh' AS nom, 'Akoua' AS prenom, '2009-11-26' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0321' AS matricule, 'Essowe' AS nom, 'Yao' AS prenom, '2009-06-17' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0322' AS matricule, 'Bassowa' AS nom, 'Kodjo' AS prenom, '2009-01-05' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0323' AS matricule, 'Kondaani' AS nom, 'Kofi' AS prenom, '2009-06-13' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0324' AS matricule, 'Batchana' AS nom, 'Selom' AS prenom, '2009-06-12' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0325' AS matricule, 'Komlan' AS nom, 'Rachida' AS prenom, '2008-09-12' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0326' AS matricule, 'Kondaani' AS nom, 'Selorm' AS prenom, '2009-04-04' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0327' AS matricule, 'Tossou' AS nom, 'Ayivi' AS prenom, '2009-09-04' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0328' AS matricule, 'Awesso' AS nom, 'Rachida' AS prenom, '2009-04-20' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0329' AS matricule, 'Tossou' AS nom, 'Nadège' AS prenom, '2008-02-05' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0330' AS matricule, 'Amétépé' AS nom, 'Fiifi' AS prenom, '2009-02-26' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0331' AS matricule, 'Ouro-Djeri' AS nom, 'Kayi' AS prenom, '2008-09-18' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0332' AS matricule, 'Tchalla' AS nom, 'Adjowa' AS prenom, '2008-12-23' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0333' AS matricule, 'Attipoe' AS nom, 'Mensah' AS prenom, '2008-02-07' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0334' AS matricule, 'Tossou' AS nom, 'Kofi' AS prenom, '2008-06-18' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0335' AS matricule, 'Gbati' AS nom, 'Mawusi' AS prenom, '2008-10-03' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0336' AS matricule, 'Dogbevi' AS nom, 'Essohanam' AS prenom, '2009-09-14' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0337' AS matricule, 'Douti' AS nom, 'Amavi' AS prenom, '2008-03-11' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0338' AS matricule, 'Gbedjeha' AS nom, 'Fiifi' AS prenom, '2009-03-27' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0339' AS matricule, 'Tetteh' AS nom, 'Edem' AS prenom, '2008-02-17' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0340' AS matricule, 'Bakai' AS nom, 'Essohanam' AS prenom, '2008-06-23' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0341' AS matricule, 'Amewuho' AS nom, 'Alfa' AS prenom, '2009-02-09' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0342' AS matricule, 'Tchamie' AS nom, 'Kudzo' AS prenom, '2009-10-18' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0343' AS matricule, 'Kondaani' AS nom, 'Anani' AS prenom, '2008-10-19' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0344' AS matricule, 'Essowe' AS nom, 'Elom' AS prenom, '2009-10-02' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0345' AS matricule, 'Agbeko' AS nom, 'Kwami' AS prenom, '2009-11-07' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0346' AS matricule, 'Padabo' AS nom, 'Akoua' AS prenom, '2008-08-21' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0347' AS matricule, 'Tchalla' AS nom, 'Edoh' AS prenom, '2009-04-26' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0348' AS matricule, 'Gbedjeha' AS nom, 'Efua' AS prenom, '2008-12-02' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0349' AS matricule, 'Agbenoto' AS nom, 'Fiifi' AS prenom, '2009-08-07' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0350' AS matricule, 'Bassowa' AS nom, 'Akoua' AS prenom, '2009-12-11' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0351' AS matricule, 'Tchamie' AS nom, 'Delali' AS prenom, '2009-09-18' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0352' AS matricule, 'Dogbe' AS nom, 'Bawa' AS prenom, '2009-02-09' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0353' AS matricule, 'Bassowa' AS nom, 'Akpene' AS prenom, '2008-01-10' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0354' AS matricule, 'Yendoubouame' AS nom, 'Sitso' AS prenom, '2009-04-28' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0355' AS matricule, 'Douti' AS nom, 'Bawa' AS prenom, '2009-04-25' AS date_naissance, 'M' AS sexe
) s, (
    SELECT cl.id
    FROM `classes` cl
    JOIN `niveaux` nv ON nv.id = cl.niveau_id
    JOIN `annees_scolaires` an ON an.id = cl.annee_scolaire_id
    WHERE nv.nom = '2nde S' AND an.active = 1
    LIMIT 1
) c
ON DUPLICATE KEY UPDATE `matricule` = VALUES(`matricule`);

-- ===== 2nde A4 : 55 élèves =====
INSERT INTO `eleves` (`matricule`, `nom`, `prenom`, `date_naissance`, `sexe`, `classe_id`, `actif`)
SELECT s.matricule, s.nom, s.prenom, s.date_naissance, s.sexe, c.id, 1
FROM (
    SELECT 'SGE2025-0356' AS matricule, 'Gbedjeha' AS nom, 'Akpedje' AS prenom, '2009-05-16' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0357' AS matricule, 'Dogbevi' AS nom, 'Kwami' AS prenom, '2009-04-07' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0358' AS matricule, 'Kodjo' AS nom, 'Afi' AS prenom, '2009-08-20' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0359' AS matricule, 'Nyavor' AS nom, 'Efua' AS prenom, '2008-02-14' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0360' AS matricule, 'Palanga' AS nom, 'Sitsofe' AS prenom, '2009-07-12' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0361' AS matricule, 'Kodjo' AS nom, 'Kossivi' AS prenom, '2008-06-18' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0362' AS matricule, 'Mensah' AS nom, 'Akossiwa' AS prenom, '2009-09-09' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0363' AS matricule, 'Ayikoue' AS nom, 'Edem' AS prenom, '2009-06-26' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0364' AS matricule, 'Alassani' AS nom, 'Afiwa' AS prenom, '2008-04-20' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0365' AS matricule, 'Agbeko' AS nom, 'Akpedje' AS prenom, '2008-01-05' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0366' AS matricule, 'Kpodar' AS nom, 'Kokoévi' AS prenom, '2009-03-20' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0367' AS matricule, 'Yendoubouame' AS nom, 'Bawa' AS prenom, '2009-03-13' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0368' AS matricule, 'Bissa' AS nom, 'Adjovi' AS prenom, '2009-12-19' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0369' AS matricule, 'Adjovi' AS nom, 'Akosua' AS prenom, '2009-06-22' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0370' AS matricule, 'Wonyu' AS nom, 'Komivi' AS prenom, '2009-12-28' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0371' AS matricule, 'Awesso' AS nom, 'Amevi' AS prenom, '2008-12-02' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0372' AS matricule, 'Amewuho' AS nom, 'Ablavi' AS prenom, '2009-03-27' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0373' AS matricule, 'Douti' AS nom, 'Kossi' AS prenom, '2008-04-01' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0374' AS matricule, 'Agbenoto' AS nom, 'Selorm' AS prenom, '2008-07-23' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0375' AS matricule, 'Attipoe' AS nom, 'Komivi' AS prenom, '2009-12-24' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0376' AS matricule, 'Amewuho' AS nom, 'Edem' AS prenom, '2008-09-11' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0377' AS matricule, 'Mensah' AS nom, 'Akpedje' AS prenom, '2009-03-15' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0378' AS matricule, 'Ouro-Djeri' AS nom, 'Afiwa' AS prenom, '2009-10-16' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0379' AS matricule, 'Sambiani' AS nom, 'Kofi' AS prenom, '2009-04-10' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0380' AS matricule, 'Klutse' AS nom, 'Nadège' AS prenom, '2009-06-16' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0381' AS matricule, 'Palanga' AS nom, 'Afiwa' AS prenom, '2009-05-04' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0382' AS matricule, 'Dogbevi' AS nom, 'Adjowa' AS prenom, '2009-03-10' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0383' AS matricule, 'Toundou' AS nom, 'Essobiyou' AS prenom, '2008-06-15' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0384' AS matricule, 'Kpodar' AS nom, 'Essowavana' AS prenom, '2008-04-27' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0385' AS matricule, 'Gbati' AS nom, 'Bertine' AS prenom, '2009-03-04' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0386' AS matricule, 'Kodjo' AS nom, 'Kofi' AS prenom, '2008-11-08' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0387' AS matricule, 'Agbenoto' AS nom, 'Sena' AS prenom, '2009-12-16' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0388' AS matricule, 'Amegan' AS nom, 'Edem' AS prenom, '2008-07-21' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0389' AS matricule, 'Kondaani' AS nom, 'Akosua' AS prenom, '2008-05-11' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0390' AS matricule, 'Kodjo' AS nom, 'Edoh' AS prenom, '2008-11-19' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0391' AS matricule, 'Amétépé' AS nom, 'Kossi' AS prenom, '2009-03-02' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0392' AS matricule, 'Tossou' AS nom, 'Kokoévi' AS prenom, '2008-12-28' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0393' AS matricule, 'Agbeko' AS nom, 'Adjowa' AS prenom, '2008-05-19' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0394' AS matricule, 'Poutouli' AS nom, 'Tchaa' AS prenom, '2009-11-18' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0395' AS matricule, 'Tchamie' AS nom, 'Adjowa' AS prenom, '2009-05-07' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0396' AS matricule, 'Bakai' AS nom, 'Tchaa' AS prenom, '2009-11-09' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0397' AS matricule, 'Bayaki' AS nom, 'Komlan' AS prenom, '2009-05-19' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0398' AS matricule, 'Yendoubouame' AS nom, 'Ayivi' AS prenom, '2009-07-17' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0399' AS matricule, 'Dogbevi' AS nom, 'Akossiwa' AS prenom, '2008-03-05' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0400' AS matricule, 'Dogbe' AS nom, 'Selorm' AS prenom, '2008-05-13' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0401' AS matricule, 'Baguilim' AS nom, 'Fiifi' AS prenom, '2009-06-10' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0402' AS matricule, 'Kondaani' AS nom, 'Sitsofe' AS prenom, '2009-12-08' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0403' AS matricule, 'Gbedjeha' AS nom, 'Enam' AS prenom, '2009-03-13' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0404' AS matricule, 'Djondo' AS nom, 'Nadège' AS prenom, '2009-06-20' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0405' AS matricule, 'Tchamie' AS nom, 'Yawo' AS prenom, '2009-03-15' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0406' AS matricule, 'Kolani' AS nom, 'Elikem' AS prenom, '2009-09-26' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0407' AS matricule, 'Batchana' AS nom, 'Kofi' AS prenom, '2008-09-15' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0408' AS matricule, 'Douti' AS nom, 'Akossiwa' AS prenom, '2008-01-27' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0409' AS matricule, 'Bakai' AS nom, 'Selom' AS prenom, '2009-09-15' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0410' AS matricule, 'Toundou' AS nom, 'Mawuli' AS prenom, '2009-02-17' AS date_naissance, 'M' AS sexe
) s, (
    SELECT cl.id
    FROM `classes` cl
    JOIN `niveaux` nv ON nv.id = cl.niveau_id
    JOIN `annees_scolaires` an ON an.id = cl.annee_scolaire_id
    WHERE nv.nom = '2nde A4' AND an.active = 1
    LIMIT 1
) c
ON DUPLICATE KEY UPDATE `matricule` = VALUES(`matricule`);

-- ===== 1ère D : 64 élèves =====
INSERT INTO `eleves` (`matricule`, `nom`, `prenom`, `date_naissance`, `sexe`, `classe_id`, `actif`)
SELECT s.matricule, s.nom, s.prenom, s.date_naissance, s.sexe, c.id, 1
FROM (
    SELECT 'SGE2025-0411' AS matricule, 'Tchamie' AS nom, 'Delali' AS prenom, '2008-08-19' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0412' AS matricule, 'Batchana' AS nom, 'Delali' AS prenom, '2008-12-01' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0413' AS matricule, 'Amegan' AS nom, 'Enyonam' AS prenom, '2007-10-03' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0414' AS matricule, 'Ouro-Djeri' AS nom, 'Sedem' AS prenom, '2007-09-02' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0415' AS matricule, 'Agbeko' AS nom, 'Amevi' AS prenom, '2008-07-06' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0416' AS matricule, 'Alassani' AS nom, 'Komivi' AS prenom, '2008-08-28' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0417' AS matricule, 'Adjei' AS nom, 'Delali' AS prenom, '2007-11-28' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0418' AS matricule, 'Amétépé' AS nom, 'Adjoa' AS prenom, '2008-09-05' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0419' AS matricule, 'Baguilim' AS nom, 'Komlan' AS prenom, '2007-05-15' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0420' AS matricule, 'Mensah' AS nom, 'Afiwa' AS prenom, '2007-04-15' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0421' AS matricule, 'Sambiani' AS nom, 'Akouavi' AS prenom, '2007-12-25' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0422' AS matricule, 'Essowe' AS nom, 'Kossi' AS prenom, '2008-10-25' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0423' AS matricule, 'Klutse' AS nom, 'Kofi' AS prenom, '2007-02-20' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0424' AS matricule, 'Adom' AS nom, 'Fiifi' AS prenom, '2007-04-24' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0425' AS matricule, 'Ayeva' AS nom, 'Kossivi' AS prenom, '2007-09-07' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0426' AS matricule, 'Attipoe' AS nom, 'Edem' AS prenom, '2008-04-27' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0427' AS matricule, 'Adom' AS nom, 'Adjovi' AS prenom, '2008-04-10' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0428' AS matricule, 'Amenyo' AS nom, 'Setodji' AS prenom, '2008-05-09' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0429' AS matricule, 'Padabo' AS nom, 'Ayivi' AS prenom, '2008-09-16' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0430' AS matricule, 'Kondaani' AS nom, 'Alfa' AS prenom, '2008-09-11' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0431' AS matricule, 'Bassowa' AS nom, 'Elikem' AS prenom, '2008-07-06' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0432' AS matricule, 'Amenyo' AS nom, 'Akpene' AS prenom, '2008-12-05' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0433' AS matricule, 'Kodjo' AS nom, 'Bertine' AS prenom, '2008-07-18' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0434' AS matricule, 'Dogbe' AS nom, 'Dogbevi' AS prenom, '2008-04-11' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0435' AS matricule, 'Alassani' AS nom, 'Fiifi' AS prenom, '2007-09-24' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0436' AS matricule, 'Sambiani' AS nom, 'Kokou' AS prenom, '2008-11-20' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0437' AS matricule, 'Sossou' AS nom, 'Yao' AS prenom, '2007-08-07' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0438' AS matricule, 'Amegan' AS nom, 'Efua' AS prenom, '2007-04-24' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0439' AS matricule, 'Dogbe' AS nom, 'Amevi' AS prenom, '2007-07-08' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0440' AS matricule, 'Poutouli' AS nom, 'Rachida' AS prenom, '2008-08-18' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0441' AS matricule, 'Awesso' AS nom, 'Efua' AS prenom, '2008-09-16' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0442' AS matricule, 'Palanga' AS nom, 'Adjoa' AS prenom, '2008-06-07' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0443' AS matricule, 'Agbenoto' AS nom, 'Selorm' AS prenom, '2007-10-28' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0444' AS matricule, 'Adjovi' AS nom, 'Selom' AS prenom, '2008-09-19' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0445' AS matricule, 'Bassowa' AS nom, 'Efua' AS prenom, '2007-06-08' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0446' AS matricule, 'Dogbe' AS nom, 'Adjovi' AS prenom, '2008-09-21' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0447' AS matricule, 'Baguilim' AS nom, 'Enyonam' AS prenom, '2008-12-21' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0448' AS matricule, 'Bakai' AS nom, 'Mawusi' AS prenom, '2008-03-05' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0449' AS matricule, 'Nyavor' AS nom, 'Ablavi' AS prenom, '2007-09-02' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0450' AS matricule, 'Baguilim' AS nom, 'Kokou' AS prenom, '2007-07-05' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0451' AS matricule, 'Toundou' AS nom, 'Yao' AS prenom, '2007-01-07' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0452' AS matricule, 'Kodjo' AS nom, 'Yawa' AS prenom, '2008-11-16' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0453' AS matricule, 'Nyavor' AS nom, 'Komlan' AS prenom, '2008-01-01' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0454' AS matricule, 'Poutouli' AS nom, 'Afiwa' AS prenom, '2007-09-27' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0455' AS matricule, 'Amétépé' AS nom, 'Kokoévi' AS prenom, '2007-02-17' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0456' AS matricule, 'Sossou' AS nom, 'Kofi' AS prenom, '2008-06-09' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0457' AS matricule, 'Alassani' AS nom, 'Akossiwa' AS prenom, '2008-08-19' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0458' AS matricule, 'Tchalla' AS nom, 'Yawo' AS prenom, '2007-11-28' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0459' AS matricule, 'Dogbevi' AS nom, 'Kwami' AS prenom, '2008-07-18' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0460' AS matricule, 'Padabo' AS nom, 'Afi' AS prenom, '2007-12-06' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0461' AS matricule, 'Tetteh' AS nom, 'Fofo' AS prenom, '2008-10-28' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0462' AS matricule, 'Agbeko' AS nom, 'Setodji' AS prenom, '2007-04-28' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0463' AS matricule, 'Agbeko' AS nom, 'Enyonam' AS prenom, '2007-11-09' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0464' AS matricule, 'Kolani' AS nom, 'Ayivi' AS prenom, '2007-04-19' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0465' AS matricule, 'Komlan' AS nom, 'Kossivi' AS prenom, '2008-03-19' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0466' AS matricule, 'Batchana' AS nom, 'Dogbevi' AS prenom, '2008-07-25' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0467' AS matricule, 'Attipoe' AS nom, 'Kwami' AS prenom, '2008-01-04' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0468' AS matricule, 'Komlan' AS nom, 'Akpene' AS prenom, '2008-10-25' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0469' AS matricule, 'Kolani' AS nom, 'Rachida' AS prenom, '2007-11-09' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0470' AS matricule, 'Amenyo' AS nom, 'Akosua' AS prenom, '2008-09-13' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0471' AS matricule, 'Batchana' AS nom, 'Ablavi' AS prenom, '2008-02-25' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0472' AS matricule, 'Dogbe' AS nom, 'Kokoévi' AS prenom, '2007-02-09' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0473' AS matricule, 'Toundou' AS nom, 'Dogbevi' AS prenom, '2007-12-13' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0474' AS matricule, 'Ayikoue' AS nom, 'Yawa' AS prenom, '2007-01-10' AS date_naissance, 'F' AS sexe
) s, (
    SELECT cl.id
    FROM `classes` cl
    JOIN `niveaux` nv ON nv.id = cl.niveau_id
    JOIN `annees_scolaires` an ON an.id = cl.annee_scolaire_id
    WHERE nv.nom = '1ère D' AND an.active = 1
    LIMIT 1
) c
ON DUPLICATE KEY UPDATE `matricule` = VALUES(`matricule`);

-- ===== 1ère A4 : 64 élèves =====
INSERT INTO `eleves` (`matricule`, `nom`, `prenom`, `date_naissance`, `sexe`, `classe_id`, `actif`)
SELECT s.matricule, s.nom, s.prenom, s.date_naissance, s.sexe, c.id, 1
FROM (
    SELECT 'SGE2025-0475' AS matricule, 'Sambiani' AS nom, 'Rachida' AS prenom, '2007-03-03' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0476' AS matricule, 'Ayeva' AS nom, 'Sedem' AS prenom, '2008-02-01' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0477' AS matricule, 'Gbati' AS nom, 'Alfa' AS prenom, '2007-10-20' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0478' AS matricule, 'Mensah' AS nom, 'Sitso' AS prenom, '2007-05-14' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0479' AS matricule, 'Adjei' AS nom, 'Rachida' AS prenom, '2007-10-17' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0480' AS matricule, 'Bakai' AS nom, 'Dogbevi' AS prenom, '2007-05-10' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0481' AS matricule, 'Bassowa' AS nom, 'Akosua' AS prenom, '2007-03-14' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0482' AS matricule, 'Tchalla' AS nom, 'Elikem' AS prenom, '2008-02-12' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0483' AS matricule, 'Palanga' AS nom, 'Akpene' AS prenom, '2008-10-20' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0484' AS matricule, 'Ayikoue' AS nom, 'Delali' AS prenom, '2007-05-01' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0485' AS matricule, 'Yendoubouame' AS nom, 'Selorm' AS prenom, '2008-06-15' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0486' AS matricule, 'Kondaani' AS nom, 'Elikem' AS prenom, '2007-05-11' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0487' AS matricule, 'Kolani' AS nom, 'Amevi' AS prenom, '2007-07-11' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0488' AS matricule, 'Kolani' AS nom, 'Essowavana' AS prenom, '2008-10-26' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0489' AS matricule, 'Mensah' AS nom, 'Bawa' AS prenom, '2008-07-12' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0490' AS matricule, 'Wonyu' AS nom, 'Foli' AS prenom, '2007-11-25' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0491' AS matricule, 'Toundou' AS nom, 'Delali' AS prenom, '2007-08-26' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0492' AS matricule, 'Kolani' AS nom, 'Amavi' AS prenom, '2007-07-22' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0493' AS matricule, 'Padabo' AS nom, 'Akouavi' AS prenom, '2008-04-09' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0494' AS matricule, 'Mensah' AS nom, 'Sedem' AS prenom, '2007-08-20' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0495' AS matricule, 'Dogbevi' AS nom, 'Adjovi' AS prenom, '2008-09-02' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0496' AS matricule, 'Nyavor' AS nom, 'Nadège' AS prenom, '2007-02-25' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0497' AS matricule, 'Bakai' AS nom, 'Alfa' AS prenom, '2007-10-21' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0498' AS matricule, 'Bissa' AS nom, 'Rachida' AS prenom, '2008-02-01' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0499' AS matricule, 'Amenyo' AS nom, 'Sitsofe' AS prenom, '2007-08-12' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0500' AS matricule, 'Batchana' AS nom, 'Mawusi' AS prenom, '2007-06-01' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0501' AS matricule, 'Poutouli' AS nom, 'Adjoa' AS prenom, '2008-02-04' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0502' AS matricule, 'Tchalla' AS nom, 'Alfa' AS prenom, '2008-08-26' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0503' AS matricule, 'Kpodar' AS nom, 'Setodji' AS prenom, '2008-08-04' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0504' AS matricule, 'Kolani' AS nom, 'Essowavana' AS prenom, '2008-06-03' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0505' AS matricule, 'Toundou' AS nom, 'Afi' AS prenom, '2007-01-28' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0506' AS matricule, 'Ayikoue' AS nom, 'Edoh' AS prenom, '2007-12-08' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0507' AS matricule, 'Bissa' AS nom, 'Elom' AS prenom, '2008-08-08' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0508' AS matricule, 'Amétépé' AS nom, 'Edoh' AS prenom, '2007-11-22' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0509' AS matricule, 'Adjovi' AS nom, 'Delali' AS prenom, '2007-08-19' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0510' AS matricule, 'Amegan' AS nom, 'Delali' AS prenom, '2007-04-09' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0511' AS matricule, 'Nyavor' AS nom, 'Sena' AS prenom, '2007-06-11' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0512' AS matricule, 'Djondo' AS nom, 'Delali' AS prenom, '2008-11-28' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0513' AS matricule, 'Padabo' AS nom, 'Akpedje' AS prenom, '2008-10-13' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0514' AS matricule, 'Djondo' AS nom, 'Adjovi' AS prenom, '2008-06-28' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0515' AS matricule, 'Amétépé' AS nom, 'Akoua' AS prenom, '2008-10-19' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0516' AS matricule, 'Kolani' AS nom, 'Edem' AS prenom, '2007-04-10' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0517' AS matricule, 'Alassani' AS nom, 'Ayivi' AS prenom, '2007-09-13' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0518' AS matricule, 'Tchamie' AS nom, 'Akoua' AS prenom, '2008-07-06' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0519' AS matricule, 'Nyavor' AS nom, 'Edoh' AS prenom, '2007-09-06' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0520' AS matricule, 'Tchamie' AS nom, 'Efua' AS prenom, '2007-06-27' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0521' AS matricule, 'Kodjo' AS nom, 'Akoua' AS prenom, '2008-01-16' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0522' AS matricule, 'Mensah' AS nom, 'Foli' AS prenom, '2008-07-22' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0523' AS matricule, 'Toundou' AS nom, 'Elikem' AS prenom, '2008-08-06' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0524' AS matricule, 'Baguilim' AS nom, 'Kodjo' AS prenom, '2007-05-02' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0525' AS matricule, 'Nyavor' AS nom, 'Akpene' AS prenom, '2008-03-25' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0526' AS matricule, 'Bayaki' AS nom, 'Adjovi' AS prenom, '2008-09-17' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0527' AS matricule, 'Sambiani' AS nom, 'Enam' AS prenom, '2008-09-27' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0528' AS matricule, 'Nyavor' AS nom, 'Fiifi' AS prenom, '2007-07-04' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0529' AS matricule, 'Agbeko' AS nom, 'Essohanam' AS prenom, '2008-05-12' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0530' AS matricule, 'Djondo' AS nom, 'Fiifi' AS prenom, '2007-04-26' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0531' AS matricule, 'Toundou' AS nom, 'Sitso' AS prenom, '2008-03-20' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0532' AS matricule, 'Klutse' AS nom, 'Mawuli' AS prenom, '2007-10-12' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0533' AS matricule, 'Nyavor' AS nom, 'Akoua' AS prenom, '2007-06-23' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0534' AS matricule, 'Douti' AS nom, 'Efua' AS prenom, '2008-09-22' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0535' AS matricule, 'Baguilim' AS nom, 'Edem' AS prenom, '2008-01-09' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0536' AS matricule, 'Dogbe' AS nom, 'Edem' AS prenom, '2007-12-11' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0537' AS matricule, 'Tossou' AS nom, 'Kossivi' AS prenom, '2007-10-21' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0538' AS matricule, 'Agbenoto' AS nom, 'Edoh' AS prenom, '2008-08-03' AS date_naissance, 'F' AS sexe
) s, (
    SELECT cl.id
    FROM `classes` cl
    JOIN `niveaux` nv ON nv.id = cl.niveau_id
    JOIN `annees_scolaires` an ON an.id = cl.annee_scolaire_id
    WHERE nv.nom = '1ère A4' AND an.active = 1
    LIMIT 1
) c
ON DUPLICATE KEY UPDATE `matricule` = VALUES(`matricule`);

-- ===== Terminale D : 70 élèves =====
INSERT INTO `eleves` (`matricule`, `nom`, `prenom`, `date_naissance`, `sexe`, `classe_id`, `actif`)
SELECT s.matricule, s.nom, s.prenom, s.date_naissance, s.sexe, c.id, 1
FROM (
    SELECT 'SGE2025-0539' AS matricule, 'Attipoe' AS nom, 'Kossivi' AS prenom, '2007-05-23' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0540' AS matricule, 'Tossou' AS nom, 'Mawusi' AS prenom, '2006-10-01' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0541' AS matricule, 'Padabo' AS nom, 'Delali' AS prenom, '2007-12-12' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0542' AS matricule, 'Klutse' AS nom, 'Kossivi' AS prenom, '2006-04-16' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0543' AS matricule, 'Poutouli' AS nom, 'Selorm' AS prenom, '2006-09-19' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0544' AS matricule, 'Bissa' AS nom, 'Fofo' AS prenom, '2007-01-02' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0545' AS matricule, 'Adjovi' AS nom, 'Delali' AS prenom, '2005-03-15' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0546' AS matricule, 'Tetteh' AS nom, 'Ama' AS prenom, '2005-12-05' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0547' AS matricule, 'Sambiani' AS nom, 'Ablavi' AS prenom, '2005-11-12' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0548' AS matricule, 'Alassani' AS nom, 'Akossiwa' AS prenom, '2007-11-06' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0549' AS matricule, 'Padabo' AS nom, 'Kossivi' AS prenom, '2006-12-23' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0550' AS matricule, 'Kondaani' AS nom, 'Sedem' AS prenom, '2005-12-04' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0551' AS matricule, 'Kpodar' AS nom, 'Mawuli' AS prenom, '2005-03-19' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0552' AS matricule, 'Batchana' AS nom, 'Setodji' AS prenom, '2006-01-01' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0553' AS matricule, 'Djondo' AS nom, 'Amavi' AS prenom, '2006-12-05' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0554' AS matricule, 'Amenyo' AS nom, 'Akossiwa' AS prenom, '2006-02-24' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0555' AS matricule, 'Kolani' AS nom, 'Sena' AS prenom, '2006-05-05' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0556' AS matricule, 'Baguilim' AS nom, 'Rachida' AS prenom, '2005-11-22' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0557' AS matricule, 'Amewuho' AS nom, 'Yao' AS prenom, '2007-01-20' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0558' AS matricule, 'Ouro-Djeri' AS nom, 'Fiifi' AS prenom, '2007-04-21' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0559' AS matricule, 'Yendoubouame' AS nom, 'Komivi' AS prenom, '2007-08-28' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0560' AS matricule, 'Ayikoue' AS nom, 'Kwami' AS prenom, '2005-02-19' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0561' AS matricule, 'Tetteh' AS nom, 'Yawa' AS prenom, '2006-03-08' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0562' AS matricule, 'Adjei' AS nom, 'Edoh' AS prenom, '2005-09-20' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0563' AS matricule, 'Kolani' AS nom, 'Rachida' AS prenom, '2005-11-10' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0564' AS matricule, 'Adom' AS nom, 'Setodji' AS prenom, '2007-04-24' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0565' AS matricule, 'Kondaani' AS nom, 'Efua' AS prenom, '2005-04-16' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0566' AS matricule, 'Dogbe' AS nom, 'Adjoa' AS prenom, '2006-11-20' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0567' AS matricule, 'Amegan' AS nom, 'Setodji' AS prenom, '2006-06-05' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0568' AS matricule, 'Douti' AS nom, 'Sitso' AS prenom, '2006-06-18' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0569' AS matricule, 'Komlan' AS nom, 'Amevi' AS prenom, '2005-10-03' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0570' AS matricule, 'Klutse' AS nom, 'Sitsofe' AS prenom, '2005-01-26' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0571' AS matricule, 'Poutouli' AS nom, 'Akpedje' AS prenom, '2007-12-17' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0572' AS matricule, 'Kolani' AS nom, 'Elikem' AS prenom, '2006-02-21' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0573' AS matricule, 'Adjei' AS nom, 'Kudzo' AS prenom, '2006-02-17' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0574' AS matricule, 'Nyavor' AS nom, 'Selom' AS prenom, '2006-07-19' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0575' AS matricule, 'Kolani' AS nom, 'Essohanam' AS prenom, '2006-12-28' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0576' AS matricule, 'Dogbe' AS nom, 'Kokou' AS prenom, '2005-07-04' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0577' AS matricule, 'Adom' AS nom, 'Akoua' AS prenom, '2005-05-22' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0578' AS matricule, 'Amegan' AS nom, 'Enam' AS prenom, '2007-03-23' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0579' AS matricule, 'Tossou' AS nom, 'Elom' AS prenom, '2006-09-17' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0580' AS matricule, 'Alassani' AS nom, 'Ablavi' AS prenom, '2005-12-25' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0581' AS matricule, 'Djondo' AS nom, 'Essowavana' AS prenom, '2005-06-26' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0582' AS matricule, 'Amewuho' AS nom, 'Enyonam' AS prenom, '2005-05-23' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0583' AS matricule, 'Gbedjeha' AS nom, 'Fofo' AS prenom, '2007-06-02' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0584' AS matricule, 'Douti' AS nom, 'Sitso' AS prenom, '2005-06-06' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0585' AS matricule, 'Ayikoue' AS nom, 'Rachida' AS prenom, '2007-11-24' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0586' AS matricule, 'Bayaki' AS nom, 'Yawo' AS prenom, '2007-01-02' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0587' AS matricule, 'Agbeko' AS nom, 'Kofi' AS prenom, '2006-06-13' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0588' AS matricule, 'Agbeko' AS nom, 'Ayivi' AS prenom, '2007-12-24' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0589' AS matricule, 'Kolani' AS nom, 'Akpene' AS prenom, '2005-07-24' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0590' AS matricule, 'Komlan' AS nom, 'Enyonam' AS prenom, '2007-06-04' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0591' AS matricule, 'Amenyo' AS nom, 'Ayivi' AS prenom, '2007-01-13' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0592' AS matricule, 'Poutouli' AS nom, 'Delali' AS prenom, '2006-06-03' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0593' AS matricule, 'Alassani' AS nom, 'Ama' AS prenom, '2005-05-19' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0594' AS matricule, 'Yendoubouame' AS nom, 'Essowavana' AS prenom, '2005-08-12' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0595' AS matricule, 'Sossou' AS nom, 'Rachida' AS prenom, '2007-09-25' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0596' AS matricule, 'Amegan' AS nom, 'Selom' AS prenom, '2006-01-08' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0597' AS matricule, 'Kolani' AS nom, 'Edoh' AS prenom, '2005-06-01' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0598' AS matricule, 'Bayaki' AS nom, 'Sitso' AS prenom, '2007-05-04' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0599' AS matricule, 'Dogbe' AS nom, 'Setodji' AS prenom, '2006-08-02' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0600' AS matricule, 'Adjei' AS nom, 'Kudzo' AS prenom, '2005-04-28' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0601' AS matricule, 'Alassani' AS nom, 'Nadège' AS prenom, '2006-12-03' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0602' AS matricule, 'Tchamie' AS nom, 'Anani' AS prenom, '2007-07-03' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0603' AS matricule, 'Essowe' AS nom, 'Sedem' AS prenom, '2005-04-10' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0604' AS matricule, 'Kolani' AS nom, 'Sitso' AS prenom, '2006-12-27' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0605' AS matricule, 'Ayeva' AS nom, 'Selorm' AS prenom, '2006-04-03' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0606' AS matricule, 'Wonyu' AS nom, 'Edem' AS prenom, '2005-03-04' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0607' AS matricule, 'Gbedjeha' AS nom, 'Fiifi' AS prenom, '2006-07-04' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0608' AS matricule, 'Klutse' AS nom, 'Rachida' AS prenom, '2006-05-15' AS date_naissance, 'F' AS sexe
) s, (
    SELECT cl.id
    FROM `classes` cl
    JOIN `niveaux` nv ON nv.id = cl.niveau_id
    JOIN `annees_scolaires` an ON an.id = cl.annee_scolaire_id
    WHERE nv.nom = 'Terminale D' AND an.active = 1
    LIMIT 1
) c
ON DUPLICATE KEY UPDATE `matricule` = VALUES(`matricule`);

-- ===== Terminale A4 : 58 élèves =====
INSERT INTO `eleves` (`matricule`, `nom`, `prenom`, `date_naissance`, `sexe`, `classe_id`, `actif`)
SELECT s.matricule, s.nom, s.prenom, s.date_naissance, s.sexe, c.id, 1
FROM (
    SELECT 'SGE2025-0609' AS matricule, 'Bakai' AS nom, 'Kwami' AS prenom, '2007-05-27' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0610' AS matricule, 'Kolani' AS nom, 'Mawuli' AS prenom, '2005-02-23' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0611' AS matricule, 'Dogbevi' AS nom, 'Alfa' AS prenom, '2007-01-22' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0612' AS matricule, 'Amétépé' AS nom, 'Enyonam' AS prenom, '2005-07-28' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0613' AS matricule, 'Bayaki' AS nom, 'Afiwa' AS prenom, '2007-04-04' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0614' AS matricule, 'Tchamie' AS nom, 'Adjoa' AS prenom, '2005-01-02' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0615' AS matricule, 'Sossou' AS nom, 'Edem' AS prenom, '2006-06-22' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0616' AS matricule, 'Komlan' AS nom, 'Sitsofe' AS prenom, '2005-02-07' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0617' AS matricule, 'Tchamie' AS nom, 'Kokoévi' AS prenom, '2005-06-04' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0618' AS matricule, 'Kodjo' AS nom, 'Delali' AS prenom, '2005-10-14' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0619' AS matricule, 'Adjovi' AS nom, 'Akosua' AS prenom, '2006-11-14' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0620' AS matricule, 'Klutse' AS nom, 'Alfa' AS prenom, '2005-05-09' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0621' AS matricule, 'Bassowa' AS nom, 'Adjowa' AS prenom, '2005-10-20' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0622' AS matricule, 'Tossou' AS nom, 'Essobiyou' AS prenom, '2006-09-16' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0623' AS matricule, 'Sambiani' AS nom, 'Kwami' AS prenom, '2006-03-14' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0624' AS matricule, 'Dogbe' AS nom, 'Setodji' AS prenom, '2007-09-26' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0625' AS matricule, 'Toundou' AS nom, 'Dogbevi' AS prenom, '2006-08-18' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0626' AS matricule, 'Douti' AS nom, 'Yawa' AS prenom, '2007-06-13' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0627' AS matricule, 'Adjovi' AS nom, 'Ablavi' AS prenom, '2006-10-08' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0628' AS matricule, 'Kodjo' AS nom, 'Kudzo' AS prenom, '2006-09-12' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0629' AS matricule, 'Ayikoue' AS nom, 'Elom' AS prenom, '2005-11-08' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0630' AS matricule, 'Bayaki' AS nom, 'Afiwa' AS prenom, '2005-04-19' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0631' AS matricule, 'Amétépé' AS nom, 'Yawa' AS prenom, '2005-04-19' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0632' AS matricule, 'Bayaki' AS nom, 'Kokoévi' AS prenom, '2007-06-19' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0633' AS matricule, 'Douti' AS nom, 'Alfa' AS prenom, '2007-03-19' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0634' AS matricule, 'Nyavor' AS nom, 'Fofo' AS prenom, '2006-03-16' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0635' AS matricule, 'Kodjo' AS nom, 'Kwami' AS prenom, '2005-10-08' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0636' AS matricule, 'Kpodar' AS nom, 'Setodji' AS prenom, '2005-06-20' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0637' AS matricule, 'Bissa' AS nom, 'Edem' AS prenom, '2007-03-26' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0638' AS matricule, 'Adjovi' AS nom, 'Afi' AS prenom, '2005-10-23' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0639' AS matricule, 'Amewuho' AS nom, 'Enam' AS prenom, '2006-02-12' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0640' AS matricule, 'Ayikoue' AS nom, 'Adjovi' AS prenom, '2006-05-11' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0641' AS matricule, 'Palanga' AS nom, 'Elom' AS prenom, '2006-11-16' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0642' AS matricule, 'Toundou' AS nom, 'Ablavi' AS prenom, '2007-12-20' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0643' AS matricule, 'Essowe' AS nom, 'Akpene' AS prenom, '2007-03-13' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0644' AS matricule, 'Kolani' AS nom, 'Essowavana' AS prenom, '2006-03-06' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0645' AS matricule, 'Douti' AS nom, 'Kossivi' AS prenom, '2005-03-20' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0646' AS matricule, 'Yendoubouame' AS nom, 'Bertine' AS prenom, '2005-11-19' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0647' AS matricule, 'Bassowa' AS nom, 'Fofo' AS prenom, '2006-12-03' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0648' AS matricule, 'Kolani' AS nom, 'Alfa' AS prenom, '2005-11-03' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0649' AS matricule, 'Amegan' AS nom, 'Mawusi' AS prenom, '2006-04-08' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0650' AS matricule, 'Awesso' AS nom, 'Alfa' AS prenom, '2005-12-01' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0651' AS matricule, 'Ayeva' AS nom, 'Dogbevi' AS prenom, '2007-07-06' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0652' AS matricule, 'Mensah' AS nom, 'Akosua' AS prenom, '2006-09-13' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0653' AS matricule, 'Douti' AS nom, 'Amevi' AS prenom, '2007-11-08' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0654' AS matricule, 'Komlan' AS nom, 'Fiifi' AS prenom, '2005-05-01' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0655' AS matricule, 'Ayikoue' AS nom, 'Enyonam' AS prenom, '2005-06-06' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0656' AS matricule, 'Palanga' AS nom, 'Ablavi' AS prenom, '2005-09-03' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0657' AS matricule, 'Yendoubouame' AS nom, 'Akoua' AS prenom, '2006-12-01' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0658' AS matricule, 'Bakai' AS nom, 'Edoh' AS prenom, '2007-07-06' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0659' AS matricule, 'Bissa' AS nom, 'Sena' AS prenom, '2005-04-14' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0660' AS matricule, 'Poutouli' AS nom, 'Kayi' AS prenom, '2006-04-04' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0661' AS matricule, 'Douti' AS nom, 'Kossivi' AS prenom, '2007-08-05' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0662' AS matricule, 'Amegan' AS nom, 'Mensah' AS prenom, '2006-02-10' AS date_naissance, 'M' AS sexe
    UNION ALL
    SELECT 'SGE2025-0663' AS matricule, 'Ayikoue' AS nom, 'Abra' AS prenom, '2005-04-06' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0664' AS matricule, 'Kpodar' AS nom, 'Akossiwa' AS prenom, '2005-05-26' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0665' AS matricule, 'Tossou' AS nom, 'Akosua' AS prenom, '2007-05-03' AS date_naissance, 'F' AS sexe
    UNION ALL
    SELECT 'SGE2025-0666' AS matricule, 'Alassani' AS nom, 'Ablavi' AS prenom, '2006-06-06' AS date_naissance, 'F' AS sexe
) s, (
    SELECT cl.id
    FROM `classes` cl
    JOIN `niveaux` nv ON nv.id = cl.niveau_id
    JOIN `annees_scolaires` an ON an.id = cl.annee_scolaire_id
    WHERE nv.nom = 'Terminale A4' AND an.active = 1
    LIMIT 1
) c
ON DUPLICATE KEY UPDATE `matricule` = VALUES(`matricule`);

-- Ajuste l'effectif maximum affiché pour chaque classe en fonction du nombre réel d'élèves insérés.
UPDATE `classes` c
JOIN `niveaux` nv ON nv.id = c.niveau_id
JOIN `annees_scolaires` an ON an.id = c.annee_scolaire_id
SET c.effectif_maximum = GREATEST(c.effectif_maximum, 70)
WHERE nv.nom = '6ème' AND an.active = 1;
UPDATE `classes` c
JOIN `niveaux` nv ON nv.id = c.niveau_id
JOIN `annees_scolaires` an ON an.id = c.annee_scolaire_id
SET c.effectif_maximum = GREATEST(c.effectif_maximum, 64)
WHERE nv.nom = '5ème' AND an.active = 1;
UPDATE `classes` c
JOIN `niveaux` nv ON nv.id = c.niveau_id
JOIN `annees_scolaires` an ON an.id = c.annee_scolaire_id
SET c.effectif_maximum = GREATEST(c.effectif_maximum, 74)
WHERE nv.nom = '4ème' AND an.active = 1;
UPDATE `classes` c
JOIN `niveaux` nv ON nv.id = c.niveau_id
JOIN `annees_scolaires` an ON an.id = c.annee_scolaire_id
SET c.effectif_maximum = GREATEST(c.effectif_maximum, 73)
WHERE nv.nom = '3ème' AND an.active = 1;
UPDATE `classes` c
JOIN `niveaux` nv ON nv.id = c.niveau_id
JOIN `annees_scolaires` an ON an.id = c.annee_scolaire_id
SET c.effectif_maximum = GREATEST(c.effectif_maximum, 74)
WHERE nv.nom = '2nde S' AND an.active = 1;
UPDATE `classes` c
JOIN `niveaux` nv ON nv.id = c.niveau_id
JOIN `annees_scolaires` an ON an.id = c.annee_scolaire_id
SET c.effectif_maximum = GREATEST(c.effectif_maximum, 55)
WHERE nv.nom = '2nde A4' AND an.active = 1;
UPDATE `classes` c
JOIN `niveaux` nv ON nv.id = c.niveau_id
JOIN `annees_scolaires` an ON an.id = c.annee_scolaire_id
SET c.effectif_maximum = GREATEST(c.effectif_maximum, 64)
WHERE nv.nom = '1ère D' AND an.active = 1;
UPDATE `classes` c
JOIN `niveaux` nv ON nv.id = c.niveau_id
JOIN `annees_scolaires` an ON an.id = c.annee_scolaire_id
SET c.effectif_maximum = GREATEST(c.effectif_maximum, 64)
WHERE nv.nom = '1ère A4' AND an.active = 1;
UPDATE `classes` c
JOIN `niveaux` nv ON nv.id = c.niveau_id
JOIN `annees_scolaires` an ON an.id = c.annee_scolaire_id
SET c.effectif_maximum = GREATEST(c.effectif_maximum, 70)
WHERE nv.nom = 'Terminale D' AND an.active = 1;
UPDATE `classes` c
JOIN `niveaux` nv ON nv.id = c.niveau_id
JOIN `annees_scolaires` an ON an.id = c.annee_scolaire_id
SET c.effectif_maximum = GREATEST(c.effectif_maximum, 58)
WHERE nv.nom = 'Terminale A4' AND an.active = 1;

SELECT 'Migration 010 : élèves de test insérés.' AS message;