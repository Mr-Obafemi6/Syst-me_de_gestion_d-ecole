<!-- app/views/bulletins/bulletin.php -->
<?php
$periodeLabel = ['', '1er Trimestre', '2ème Trimestre', '3ème Trimestre'][$periode] ?? '';
$nomEcole     = $params['nom_ecole'] ?? 'Groupe Scolaire';
$adresse      = $params['adresse']   ?? 'Lomé, Togo';
$telephone    = $params['telephone'] ?? '';
$anneeSco     = $eleve['annee_scolaire'] ?? date('Y') . '-' . (date('Y')+1);

// Couleur selon moyenne
function couleurMoy(float $m): string {
    if ($m >= 14) return 'moy-excellent';
    if ($m >= 12) return 'moy-bien';
    if ($m >= 10) return 'moy-passable';
    return $m > 0 ? 'moy-insuff' : '';
}
?>

<?php if (!$print): ?>
<!-- Barre d'actions (mode normal) -->
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <a href="<?= Router::url('bulletins') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Retour
        </a>
    </div>
    <div class="d-flex gap-2">
        <?php foreach ([1,2,3] as $p): ?>
        <a href="<?= Router::url('bulletins/eleve/' . $eleve['id'] . '?periode=' . $p) ?>"
           class="btn btn-sm <?= $periode == $p ? 'btn-primary' : 'btn-outline-secondary' ?>">
            <?= ['','1er Trim.','2ème Trim.','3ème Trim.'][$p] ?>
        </a>
        <?php endforeach; ?>
        <a href="<?= Router::url('bulletins/eleve/' . $eleve['id'] . '?periode=' . $periode . '&print=1') ?>"
           target="_blank" class="btn btn-sm btn-danger">
            <i class="bi bi-printer me-1"></i> Imprimer / PDF
        </a>
    </div>
</div>
<?php endif; ?>

<div class="page">

    <!-- EN-TÊTE -->
    <div class="header">
        <div class="header-top">
            <div>
                <div class="school-name"><?= htmlspecialchars($nomEcole) ?></div>
                <div class="school-info">
                    <?= htmlspecialchars($adresse) ?>
                    <?= $telephone ? ' — Tél: ' . htmlspecialchars($telephone) : '' ?>
                </div>
            </div>
            <div style="text-align:right">
                <div class="school-info">Année scolaire : <strong><?= htmlspecialchars($anneeSco) ?></strong></div>
                <div class="school-info">Classe : <strong><?= htmlspecialchars($eleve['classe_nom'] ?? '') ?></strong></div>
            </div>
        </div>
        <div class="bulletin-title">
            Bulletin de notes
            <span class="periode-badge"><?= $periodeLabel ?></span>
        </div>
    </div>

    <!-- INFOS ÉLÈVE -->
    <div class="eleve-info">
        <div class="info-item">
            <div class="info-label">NOM & PRÉNOM</div>
            <div class="info-value"><strong><?= htmlspecialchars($eleve['nom'] . ' ' . $eleve['prenom']) ?></strong></div>
        </div>
        <div class="info-item">
            <div class="info-label">MATRICULE</div>
            <div class="info-value"><?= htmlspecialchars($eleve['matricule']) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">DATE DE NAISSANCE</div>
            <div class="info-value">
                <?= $eleve['date_naissance'] ? date('d/m/Y', strtotime($eleve['date_naissance'])) : '—' ?>
            </div>
        </div>
        <div class="info-item">
            <div class="info-label">CLASSE</div>
            <div class="info-value"><?= htmlspecialchars($eleve['classe_nom'] ?? '—') ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">NIVEAU</div>
            <div class="info-value"><?= htmlspecialchars($eleve['classe_niveau'] ?? '—') ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">SEXE</div>
            <div class="info-value"><?= $eleve['sexe'] === 'M' ? 'Masculin' : 'Féminin' ?></div>
        </div>
    </div>

    <!-- TABLEAU DES NOTES -->
    <?php if (empty($moyennes)): ?>
    <div style="text-align:center;padding:30px;color:#888;border:1px dashed #ccc;border-radius:4px;margin-bottom:12px">
        Aucune note enregistrée pour ce trimestre.
    </div>
    <?php else: ?>
    <table class="notes-table">
        <thead>
            <tr>
                <th style="width:35%">Matière</th>
                <th class="center" style="width:10%">Coef.</th>
                <th class="center" style="width:10%">Nb devoirs</th>
                <th class="center" style="width:10%">Note min</th>
                <th class="center" style="width:10%">Note max</th>
                <th class="center" style="width:12%">Moyenne /20</th>
                <th class="center" style="width:13%">Moy. pond.</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sumCoef   = 0;
        $sumPond   = 0;
        foreach ($moyennes as $mat):
            $pond = $mat['moyenne'] * $mat['coefficient'];
            $sumCoef += $mat['coefficient'];
            $sumPond += $pond;
            $cls = couleurMoy((float)$mat['moyenne']);
        ?>
        <tr>
            <td class="matiere"><?= htmlspecialchars($mat['matiere_nom']) ?></td>
            <td class="center"><?= $mat['coefficient'] ?></td>
            <td class="center"><?= $mat['nb_notes'] ?></td>
            <td class="center"><?= number_format($mat['note_min'], 2) ?></td>
            <td class="center"><?= number_format($mat['note_max'], 2) ?></td>
            <td class="center"><span class="<?= $cls ?>"><?= number_format($mat['moyenne'], 2) ?></span></td>
            <td class="center"><?= number_format($pond, 2) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">Total coefficients : <?= $sumCoef ?></td>
                <td colspan="4" class="center">MOYENNE GÉNÉRALE</td>
                <td class="center" style="font-size:12pt">
                    <?= $sumCoef > 0 ? number_format($sumPond / $sumCoef, 2) : '—' ?> /20
                </td>
            </tr>
        </tfoot>
    </table>
    <?php endif; ?>

    <!-- BILAN -->
    <div class="bilan">
        <div class="bilan-card">
            <div class="bilan-value <?= couleurMoy($moyGen) ?>">
                <?= $moyGen > 0 ? number_format($moyGen, 2) : '—' ?>
            </div>
            <div class="bilan-label">Moyenne générale /20</div>
        </div>
        <div class="bilan-card">
            <div class="bilan-value"><?= $rang ?><sup style="font-size:10pt">e</sup></div>
            <div class="bilan-label">Rang sur <?= $totalEleves ?> élèves</div>
        </div>
        <div class="bilan-card">
            <div class="bilan-value" style="font-size:12pt;padding-top:4px"><?= htmlspecialchars($mention) ?></div>
            <div class="bilan-label">Mention</div>
        </div>
        <div class="bilan-card">
            <div class="bilan-value" style="font-size:12pt;padding-top:4px">
                <?= $moyGen >= 10 ? '✓' : '✗' ?>
            </div>
            <div class="bilan-label"><?= $moyGen >= 10 ? 'Admis(e)' : 'Non admis(e)' ?></div>
        </div>
    </div>

    <!-- APPRÉCIATION -->
    <div class="appreciation">
        <div class="appreciation-label">Appréciation du Directeur / Conseil de classe :</div>
        <div style="height:35px"></div>
    </div>

    <!-- SIGNATURES -->
    <div class="signatures">
        <div class="signature-box">
            Le Directeur<br>
            <div style="height:40px"></div>
        </div>
        <div class="signature-box">
            Le Professeur Principal<br>
            <div style="height:40px"></div>
        </div>
        <div class="signature-box">
            Parent / Tuteur<br>
            <div style="height:40px"></div>
        </div>
    </div>

    <!-- PIED DE PAGE -->
    <div class="footer">
        <?= htmlspecialchars($nomEcole) ?> — <?= htmlspecialchars($adresse) ?>
        — Bulletin généré le <?= date('d/m/Y à H:i') ?>
    </div>

</div><!-- /.page -->
