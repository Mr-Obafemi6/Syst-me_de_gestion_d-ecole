<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Bulletin') ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Times New Roman', serif;
            font-size: 11pt;
            color: #000;
            background: #f0f0f0;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            background: white;
            padding: 15mm 15mm 10mm 15mm;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
        }

        /* ===== EN-TÊTE ===== */
        .header {
            border: 2px solid #1a1a2e;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 12px;
        }
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        .school-name {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #1a1a2e;
        }
        .school-info { font-size: 9pt; color: #444; }
        .bulletin-title {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #1a1a2e;
            border-top: 1px solid #1a1a2e;
            padding-top: 6px;
        }
        .periode-badge {
            display: inline-block;
            background: #1a1a2e;
            color: white;
            padding: 2px 10px;
            border-radius: 3px;
            font-size: 9pt;
            margin-left: 8px;
        }

        /* ===== INFOS ÉLÈVE ===== */
        .eleve-info {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 6px;
            border: 1px solid #ccc;
            padding: 8px;
            margin-bottom: 12px;
            border-radius: 3px;
            background: #f9f9f9;
        }
        .info-item { font-size: 10pt; }
        .info-label { font-weight: bold; color: #555; font-size: 8pt; }
        .info-value { font-size: 10pt; }

        /* ===== TABLEAU DES NOTES ===== */
        .notes-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 10pt;
        }
        .notes-table th {
            background: #1a1a2e;
            color: white;
            padding: 6px 8px;
            text-align: center;
            font-size: 9pt;
        }
        .notes-table th:first-child { text-align: left; }
        .notes-table td {
            border: 1px solid #ddd;
            padding: 5px 8px;
            vertical-align: middle;
        }
        .notes-table tbody tr:nth-child(even) { background: #f8f8f8; }
        .notes-table td.matiere { font-weight: bold; }
        .notes-table td.center { text-align: center; }
        .notes-table tfoot td {
            background: #1a1a2e;
            color: white;
            font-weight: bold;
            padding: 6px 8px;
        }
        .notes-table tfoot td.center { text-align: center; }

        /* Couleurs des moyennes */
        .moy-excellent { color: #1a7a4a; font-weight: bold; }
        .moy-bien      { color: #1a5276; font-weight: bold; }
        .moy-passable  { color: #b7770d; font-weight: bold; }
        .moy-insuff    { color: #c0392b; font-weight: bold; }

        /* ===== BILAN ===== */
        .bilan {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 8px;
            margin-bottom: 12px;
        }
        .bilan-card {
            border: 2px solid #1a1a2e;
            border-radius: 4px;
            padding: 8px;
            text-align: center;
        }
        .bilan-value {
            font-size: 18pt;
            font-weight: bold;
            color: #1a1a2e;
        }
        .bilan-label { font-size: 8pt; color: #666; }

        /* ===== APPRÉCIATION ===== */
        .appreciation {
            border: 1px solid #ccc;
            padding: 8px 12px;
            margin-bottom: 12px;
            border-radius: 3px;
            min-height: 50px;
        }
        .appreciation-label {
            font-size: 9pt;
            font-weight: bold;
            color: #555;
            margin-bottom: 4px;
        }

        /* ===== SIGNATURES ===== */
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }
        .signature-box {
            text-align: center;
            border-top: 1px solid #999;
            padding-top: 4px;
            font-size: 9pt;
            color: #555;
        }

        /* ===== PIED DE PAGE ===== */
        .footer {
            text-align: center;
            font-size: 8pt;
            color: #888;
            margin-top: 10px;
            border-top: 1px solid #ddd;
            padding-top: 6px;
        }

        /* ===== BARRE D'ACTIONS (non imprimée) ===== */
        .print-bar {
            position: fixed;
            top: 0; left: 0; right: 0;
            background: #1a1a2e;
            color: white;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 1000;
            gap: 10px;
        }
        .print-bar a, .print-bar button {
            color: white;
            text-decoration: none;
            padding: 6px 14px;
            border-radius: 4px;
            border: 1px solid rgba(255,255,255,0.3);
            background: rgba(255,255,255,0.1);
            cursor: pointer;
            font-size: 13px;
        }
        .print-bar .btn-print {
            background: #e94560;
            border-color: #e94560;
        }
        body.has-bar .page { margin-top: 60px; }

        /* ===== IMPRESSION ===== */
        @media print {
            body { background: white; }
            .print-bar { display: none !important; }
            .page {
                margin: 0;
                padding: 10mm;
                box-shadow: none;
                width: 100%;
            }
        }
    </style>
</head>
<body class="has-bar">

<!-- Barre d'actions -->
<div class="print-bar">
    <div>
        <strong>📄 Bulletin de notes</strong> —
        <?= htmlspecialchars($eleve['prenom'] . ' ' . $eleve['nom']) ?>
    </div>
    <div style="display:flex;gap:8px">
        <a href="<?= Router::url('bulletins/eleve/' . $eleve['id'] . '?periode=' . $periode) ?>">
            ← Retour
        </a>
        <button class="btn-print" onclick="window.print()">
            🖨️ Imprimer / Enregistrer PDF
        </button>
    </div>
</div>

<?= $content ?>

<script>
// Auto-print si demandé
<?php if ($print): ?>
window.addEventListener('load', () => setTimeout(() => window.print(), 500));
<?php endif; ?>
</script>
</body>
</html>
