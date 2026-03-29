<!-- app/views/paiements/recu.php -->
<?php
$nomEcole  = $params['nom_ecole'] ?? 'Groupe Scolaire';
$adresse   = $params['adresse']   ?? 'Lomé, Togo';
$telephone = $params['telephone'] ?? '';
$modeLabel = ['especes' => 'Espèces', 'mobile_money' => 'Mobile Money', 'virement' => 'Virement'];
?>

<div class="recu-wrapper">

    <!-- En-tête -->
    <div class="recu-header">
        <div class="school-name"><?= htmlspecialchars($nomEcole) ?></div>
        <div class="school-sub">
            <?= htmlspecialchars($adresse) ?>
            <?= $telephone ? ' — ' . htmlspecialchars($telephone) : '' ?>
        </div>
    </div>

    <div class="recu-title">REÇU DE PAIEMENT</div>

    <div class="recu-num">
        N° <strong><?= htmlspecialchars($paiement['recu_numero']) ?></strong>
        &nbsp;&nbsp;|&nbsp;&nbsp;
        Année : <strong><?= htmlspecialchars($paiement['annee_libelle']) ?></strong>
    </div>

    <!-- Montant -->
    <div class="montant-box">
        <div class="montant-label">Montant perçu</div>
        <div class="montant-value">
            <?= number_format($paiement['montant_fcfa'], 0, ',', ' ') ?> FCFA
        </div>
        <div class="mt-1">
            <span class="statut-badge statut-<?= $paiement['statut'] ?>">
                <?= $paiement['statut'] === 'paye' ? '✅ Payé intégralement' : '⏳ Paiement partiel' ?>
            </span>
        </div>
    </div>

    <!-- Infos -->
    <table class="infos">
        <tr>
            <td>Élève</td>
            <td><strong><?= htmlspecialchars($paiement['eleve_prenom'] . ' ' . $paiement['eleve_nom']) ?></strong></td>
        </tr>
        <tr>
            <td>Matricule</td>
            <td><?= htmlspecialchars($paiement['matricule']) ?></td>
        </tr>
        <tr>
            <td>Classe</td>
            <td><?= htmlspecialchars($paiement['classe_nom']) ?> — <?= htmlspecialchars($paiement['niveau']) ?></td>
        </tr>
        <tr>
            <td>Date de paiement</td>
            <td><?= date('d/m/Y', strtotime($paiement['date_paiement'])) ?></td>
        </tr>
        <tr>
            <td>Mode de paiement</td>
            <td><?= $modeLabel[$paiement['mode_paiement']] ?? $paiement['mode_paiement'] ?></td>
        </tr>
        <?php if ($paiement['commentaire']): ?>
        <tr>
            <td>Commentaire</td>
            <td><?= htmlspecialchars($paiement['commentaire']) ?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <td>Enregistré par</td>
            <td><?= htmlspecialchars($paiement['createur_prenom'] . ' ' . $paiement['createur_nom']) ?></td>
        </tr>
    </table>

    <!-- Total payé cette année -->
    <div class="total-annee">
        <strong>Total payé cette année scolaire :</strong>
        <span style="float:right;color:#1a7a4a;font-weight:bold">
            <?= number_format($totalPaye, 0, ',', ' ') ?> FCFA
        </span>
    </div>

    <!-- Signatures -->
    <div class="signatures">
        <div class="sig-box">
            <div></div>
            Le Caissier / Comptable
        </div>
        <div class="sig-box">
            <div></div>
            Le Parent / Tuteur
        </div>
    </div>

    <!-- QR Code de vérification -->
    <div style="display:flex;align-items:center;gap:12px;margin-top:12px;
                border-top:1px dashed #ddd;padding-top:10px;">
        <div id="qrcode-recu" style="flex-shrink:0"></div>
        <div style="font-size:8pt;color:#888;line-height:1.4">
            <strong>Reçu N° <?= htmlspecialchars($paiement['recu_numero']) ?></strong><br>
            Scannez pour vérifier<br>
            <?= htmlspecialchars($nomEcole) ?>
        </div>
    </div>

    <div class="footer">
        <?= htmlspecialchars($nomEcole) ?> — Reçu généré le <?= date('d/m/Y à H:i') ?>
    </div>
</div>

<!-- QR Code JS (génération côté client) -->
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
const recuData = [
    'SGE Reçu: <?= htmlspecialchars($paiement['recu_numero']) ?>',
    'Élève: <?= htmlspecialchars($paiement['eleve_prenom'] . ' ' . $paiement['eleve_nom']) ?>',
    'Montant: <?= number_format($paiement['montant_fcfa'], 0, ',', ' ') ?> FCFA',
    'Date: <?= date('d/m/Y', strtotime($paiement['date_paiement'])) ?>',
    'Statut: <?= $paiement['statut'] ?>',
].join(' | ');

QRCode.toCanvas(document.createElement('canvas'), recuData, {
    width: 80,
    margin: 1,
    color: { dark: '#1a1a2e', light: '#ffffff' }
}, function(err, canvas) {
    if (!err) {
        document.getElementById('qrcode-recu').appendChild(canvas);
    }
});
</script>
