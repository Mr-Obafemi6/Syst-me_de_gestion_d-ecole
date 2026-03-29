<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'Bulletins en lot') ?></title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Times New Roman', serif; font-size:10pt; background:#e8e8e8; }

        /* Barre d'actions — cachée à l'impression */
        .print-bar {
            position:fixed; top:0; left:0; right:0;
            background:#1a1a2e; color:white;
            padding:10px 20px;
            display:flex; align-items:center; justify-content:space-between;
            z-index:9999; gap:12px;
        }
        .print-bar a, .print-bar button {
            color:white; text-decoration:none;
            padding:6px 14px; border-radius:4px;
            border:1px solid rgba(255,255,255,0.3);
            background:rgba(255,255,255,0.1);
            cursor:pointer; font-size:13px;
        }
        .print-bar .btn-print { background:#e94560; border-color:#e94560; font-weight:600; }
        .print-bar .info { font-size:12px; opacity:0.8; }

        /* Chaque bulletin = une page A4 */
        .bulletin-page {
            width: 210mm;
            min-height: 297mm;
            margin: 70px auto 20px;
            background: white;
            padding: 12mm 12mm 8mm;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            page-break-after: always;
        }
        .bulletin-page:last-child { page-break-after: avoid; }

        /* En-tête */
        .b-header {
            border: 2px solid #1a1a2e;
            border-radius:3px; padding:8px; margin-bottom:10px;
        }
        .b-header-top { display:flex; justify-content:space-between; align-items:center; margin-bottom:6px; }
        .school-name { font-size:13pt; font-weight:bold; text-transform:uppercase; color:#1a1a2e; }
        .school-sub  { font-size:8pt; color:#666; }
        .b-title {
            text-align:center; background:#1a1a2e; color:white;
            padding:5px; font-size:11pt; font-weight:bold;
            letter-spacing:1px;
        }
        .periode-badge {
            display:inline-block; background:#e94560; color:white;
            padding:1px 8px; border-radius:2px; font-size:8pt; margin-left:6px;
        }

        /* Infos élève */
        .b-eleve {
            display:grid; grid-template-columns:1fr 1fr 1fr;
            gap:4px; background:#f8f8f8;
            border:1px solid #ddd; padding:6px; margin-bottom:8px; border-radius:2px;
        }
        .b-eleve div { font-size:9pt; }
        .b-eleve span { font-weight:bold; color:#333; font-size:8pt; display:block; }

        /* Tableau des notes */
        .b-table { width:100%; border-collapse:collapse; margin-bottom:8px; font-size:9pt; }
        .b-table th {
            background:#1a1a2e; color:white;
            padding:4px 6px; text-align:center; font-size:8pt;
        }
        .b-table th:first-child { text-align:left; }
        .b-table td { border:1px solid #ddd; padding:4px 6px; vertical-align:middle; }
        .b-table tbody tr:nth-child(even) { background:#f9f9f9; }
        .b-table tfoot td { background:#1a1a2e; color:white; font-weight:bold; padding:4px 6px; }
        .b-table td.center { text-align:center; }
        .b-table td.mat { font-weight:bold; }

        .moy-ok   { color:#1a7a4a; font-weight:bold; }
        .moy-mid  { color:#1a5276; font-weight:bold; }
        .moy-low  { color:#b7770d; font-weight:bold; }
        .moy-fail { color:#c0392b; font-weight:bold; }

        /* Bilan */
        .b-bilan {
            display:grid; grid-template-columns:1fr 1fr 1fr 1fr;
            gap:6px; margin-bottom:8px;
        }
        .b-bilan-card {
            border:1.5px solid #1a1a2e; border-radius:3px;
            padding:5px; text-align:center;
        }
        .b-bilan-val { font-size:14pt; font-weight:bold; color:#1a1a2e; }
        .b-bilan-lbl { font-size:7pt; color:#666; }

        /* Appréciation + signatures */
        .b-appre {
            border:1px solid #ddd; padding:6px; min-height:40px;
            margin-bottom:8px; font-size:8pt; color:#555;
        }
        .b-sigs { display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; margin-top:10px; }
        .b-sig  { text-align:center; border-top:1px solid #aaa; padding-top:3px; font-size:8pt; color:#555; }
        .b-sig div { height:30px; }

        .b-footer {
            text-align:center; font-size:7pt; color:#aaa;
            margin-top:6px; border-top:1px solid #eee; padding-top:4px;
        }

        /* Aucune note */
        .b-empty {
            text-align:center; padding:20px; color:#999;
            border:1px dashed #ccc; border-radius:3px; margin-bottom:8px; font-size:9pt;
        }

        /* IMPRESSION */
        @media print {
            body { background:white; }
            .print-bar { display:none !important; }
            .bulletin-page {
                margin:0; box-shadow:none;
                width:100%; padding:8mm;
            }
        }
    </style>
</head>
<body>

<!-- Barre d'actions -->
<div class="print-bar">
    <div>
        <strong>📋 Bulletins en lot</strong> —
        <?= htmlspecialchars($classe['nom']) ?> —
        <?= ['','1er Trimestre','2ème Trimestre','3ème Trimestre'][$periode] ?>
        <span class="info">&nbsp;(<?= count($bulletins) ?> bulletins)</span>
    </div>
    <div style="display:flex;gap:8px;align-items:center">
        <a href="<?= Router::url('bulletins/classe/' . $classe['id'] . '?periode=' . $periode) ?>">
            ← Retour
        </a>
        <button class="btn-print" onclick="window.print()">
            🖨️ Imprimer tous les bulletins
        </button>
    </div>
</div>

<?= $content ?>

</body>
</html>
