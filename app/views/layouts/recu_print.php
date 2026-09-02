<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'Reçu') ?></title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: 'Arial', sans-serif; background:#f0f0f0; font-size:11pt; }

        .print-bar {
            position:fixed; top:0; left:0; right:0;
            background:#1a1a2e; color:white;
            padding:10px 20px;
            display:flex; align-items:center; justify-content:space-between;
            z-index:1000;
        }
        .print-bar a, .print-bar button {
            color:white; text-decoration:none;
            padding:6px 14px; border-radius:4px;
            border:1px solid rgba(255,255,255,0.3);
            background:rgba(255,255,255,0.1);
            cursor:pointer; font-size:13px;
        }
        .print-bar .btn-print { background:#28a745; border-color:#28a745; }

        .recu-wrapper {
            margin: 70px auto 20px;
            max-width: 148mm; /* A5 */
            background: white;
            padding: 12mm;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            border-top: 6px solid #1a1a2e;
        }

        .recu-header { text-align:center; margin-bottom:12px; }
        .school-name { font-size:14pt; font-weight:bold; color:#1a1a2e; text-transform:uppercase; }
        .school-sub  { font-size:9pt; color:#666; }

        .recu-title {
            text-align:center;
            background:#1a1a2e; color:white;
            padding:6px; font-size:12pt; font-weight:bold;
            letter-spacing:2px; margin:10px 0;
        }

        .recu-num {
            text-align:center; font-size:10pt;
            color:#666; margin-bottom:12px;
            border-bottom:1px dashed #ccc; padding-bottom:8px;
        }

        table.infos { width:100%; border-collapse:collapse; margin-bottom:12px; }
        table.infos td { padding:5px 8px; font-size:10pt; border:1px solid #eee; }
        table.infos td:first-child { font-weight:bold; color:#555; width:40%; background:#f9f9f9; }

        .montant-box {
            text-align:center; border:2px solid #1a1a2e;
            border-radius:6px; padding:12px; margin:12px 0;
        }
        .montant-label { font-size:9pt; color:#666; }
        .montant-value { font-size:22pt; font-weight:bold; color:#1a7a4a; }

        .statut-badge {
            display:inline-block; padding:3px 12px; border-radius:3px;
            font-weight:bold; font-size:9pt;
        }
        .statut-paye    { background:#d4edda; color:#155724; }
        .statut-partiel { background:#fff3cd; color:#856404; }

        .signatures {
            display:grid; grid-template-columns:1fr 1fr;
            gap:20px; margin-top:16px;
        }
        .sig-box { text-align:center; border-top:1px solid #999; padding-top:4px; font-size:9pt; color:#555; }
        .sig-box div { height:35px; }

        .footer {
            text-align:center; font-size:8pt; color:#999;
            margin-top:10px; border-top:1px dashed #ddd; padding-top:6px;
        }

        .total-annee {
            background:#f0f4ff; border:1px solid #c7d5f8;
            border-radius:4px; padding:8px 12px; margin-top:10px;
            font-size:10pt;
        }

        @media print {
            body { background:white; }
            .print-bar { display:none !important; }
            .recu-wrapper { margin:0; box-shadow:none; max-width:100%; }
        }
    </style>
</head>
<body>
<div class="print-bar">
    <span>🧾 Reçu — <?= htmlspecialchars($paiement['recu_numero']) ?></span>
    <div style="display:flex;gap:8px">
        <a href="<?= Router::url('paiements') ?>">← Retour</a>
        <button class="btn-print" onclick="window.print()">🖨️ Imprimer</button>
    </div>
</div>
<?= $content ?>
</body>
</html>
