<!-- app/views/bulletins/lot.php -->
<?php
$periodeLabel = ['','1er Trimestre','2ème Trimestre','3ème Trimestre'][$periode] ?? '';

function couleurMoyLot(float $m): string {
    if ($m >= 14) return 'moy-ok';
    if ($m >= 12) return 'moy-mid';
    if ($m >= 10) return 'moy-low';
    return $m > 0 ? 'moy-fail' : '';
}
?>

<?php foreach ($bulletins as $b):
    $eleve    = $b['eleve'];
    $moyennes = $b['moyennes'];
    $moyGen   = $b['moyGen'];
?>

<div class="bulletin-page">

    <!-- En-tête -->
    <div class="b-header">
        <div class="b-header-top">
            <div>
                <div class="school-name"><?= htmlspecialchars($nomEcole) ?></div>
                <div class="school-sub">
                    <?= htmlspecialchars($adresse) ?>
                    <?= $telephone ? ' — Tél: ' . htmlspecialchars($telephone) : '' ?>
                </div>
            </div>
            <div style="text-align:right;font-size:8pt;color:#555">
                Classe : <strong><?= htmlspecialchars($classe['nom']) ?></strong><br>
                Niveau : <?= htmlspecialchars($classe['niveau']) ?>
            </div>
        </div>
        <div class="b-title">
            Bulletin de notes
            <span class="periode-badge"><?= $periodeLabel ?></span>
        </div>
    </div>

    <!-- Infos élève -->
    <div class="b-eleve">
        <div>
            <span>NOM & PRÉNOM</span>
            <strong><?= htmlspecialchars($eleve['nom'] . ' ' . $eleve['prenom']) ?></strong>
        </div>
        <div>
            <span>MATRICULE</span>
            <?= htmlspecialchars($eleve['matricule']) ?>
        </div>
        <div>
            <span>RANG</span>
            <?= $b['rang'] ?><sup>e</sup> / <?= $b['totalEleves'] ?> élèves
        </div>
    </div>

    <!-- Tableau notes -->
    <?php if (empty($moyennes)): ?>
    <div class="b-empty">Aucune note enregistrée pour ce trimestre.</div>
    <?php else: ?>
    <?php
    $sumCoef = 0; $sumPond = 0;
    foreach ($moyennes as $m) {
        $sumCoef += $m['coefficient'];
        $sumPond += $m['moyenne'] * $m['coefficient'];
    }
    ?>
    <table class="b-table">
        <thead>
            <tr>
                <th style="width:35%">Matière</th>
                <th class="center" style="width:8%">Coef.</th>
                <th class="center" style="width:8%">Nb</th>
                <th class="center" style="width:10%">Min</th>
                <th class="center" style="width:10%">Max</th>
                <th class="center" style="width:12%">Moy./20</th>
                <th class="center" style="width:12%">Pond.</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($moyennes as $m): ?>
        <tr>
            <td class="mat"><?= htmlspecialchars($m['matiere_nom']) ?></td>
            <td class="center"><?= $m['coefficient'] ?></td>
            <td class="center"><?= $m['nb_notes'] ?></td>
            <td class="center"><?= number_format($m['note_min'], 2) ?></td>
            <td class="center"><?= number_format($m['note_max'], 2) ?></td>
            <td class="center">
                <span class="<?= couleurMoyLot((float)$m['moyenne']) ?>">
                    <?= number_format($m['moyenne'], 2) ?>
                </span>
            </td>
            <td class="center"><?= number_format($m['moyenne'] * $m['coefficient'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">Total coeff. : <?= $sumCoef ?></td>
                <td colspan="4" class="center">MOYENNE GÉNÉRALE</td>
                <td class="center" style="font-size:11pt">
                    <?= $sumCoef > 0 ? number_format($sumPond / $sumCoef, 2) : '—' ?>/20
                </td>
            </tr>
        </tfoot>
    </table>
    <?php endif; ?>

    <!-- Bilan -->
    <div class="b-bilan">
        <div class="b-bilan-card">
            <div class="b-bilan-val <?= couleurMoyLot($moyGen) ?>">
                <?= $moyGen > 0 ? number_format($moyGen, 2) : '—' ?>
            </div>
            <div class="b-bilan-lbl">Moy. générale /20</div>
        </div>
        <div class="b-bilan-card">
            <div class="b-bilan-val"><?= $b['rang'] ?><sup style="font-size:8pt">e</sup></div>
            <div class="b-bilan-lbl">Rang sur <?= $b['totalEleves'] ?></div>
        </div>
        <div class="b-bilan-card">
            <div class="b-bilan-val" style="font-size:10pt;padding-top:4px">
                <?= htmlspecialchars($b['mention']) ?>
            </div>
            <div class="b-bilan-lbl">Mention</div>
        </div>
        <div class="b-bilan-card">
            <div class="b-bilan-val" style="font-size:12pt;padding-top:4px">
                <?= $moyGen >= 10 ? '✓' : '✗' ?>
            </div>
            <div class="b-bilan-lbl"><?= $moyGen >= 10 ? 'Admis(e)' : 'Non admis(e)' ?></div>
        </div>
    </div>

    <!-- Appréciation -->
    <div class="b-appre">
        <strong style="font-size:8pt;color:#555">Appréciation :</strong>
        <div style="height:22px"></div>
    </div>

    <!-- Signatures -->
    <div class="b-sigs">
        <div class="b-sig"><div></div>Le Directeur</div>
        <div class="b-sig"><div></div>Le Professeur Principal</div>
        <div class="b-sig"><div></div>Parent / Tuteur</div>
    </div>

    <div class="b-footer">
        <?= htmlspecialchars($nomEcole) ?> — Bulletin généré le <?= date('d/m/Y') ?>
    </div>

</div><!-- /.bulletin-page -->

<?php endforeach; ?>
