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
            <div class="school-brand">
                <?php
                    $topbarLogo = !empty($app_logo) ? $app_logo : null;
                ?>
                <?php if (!empty($topbarLogo)): ?>
                    <img src="<?= BASE_URL ?>/<?= htmlspecialchars($topbarLogo) ?>" alt="Logo" class="school-logo">
                <?php endif; ?>
                <div>
                    <div class="school-name"><?= htmlspecialchars($nomEcole) ?></div>
                    <div class="school-info">
                        <?= htmlspecialchars($adresse) ?>
                        <?= $telephone ? ' — Tél: ' . htmlspecialchars($telephone) : '' ?>
                    </div>
                </div>
            </div>
            <div class="school-meta">
                <div class="school-header-label">République Togolaise</div>
                <div class="school-header-label">Ministère de l'Enseignement Primaire et Secondaire</div>
                <div class="school-header-label">Collège d'Enseignement Général</div>
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
            <div class="info-label">NOM DE L'ÉLÈVE</div>
            <div class="info-value"><strong><?= htmlspecialchars($eleve['nom'] . ' ' . $eleve['prenom']) ?></strong></div>
        </div>
        <div class="info-item">
            <div class="info-label">ANNÉE SCOLAIRE</div>
            <div class="info-value"><strong><?= htmlspecialchars($anneeSco) ?></strong></div>
        </div>
            <div class="info-item">
            <div class="info-label">CLASSE</div>
            <div class="info-value"><?= htmlspecialchars($eleve['classe_nom'] ?? '—') ?></div>
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
            <div class="info-label">TRIMESTRE</div>
            <div class="info-value"><?= htmlspecialchars($periodeLabel ?: '—') ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">EFFECTIF</div>
            <div class="info-value"><?= $totalEleves ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">NIVEAU</div>
            <div class="info-value"><?= htmlspecialchars($eleve['classe_niveau'] ?? '—') ?></div>
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
                <th style="width:20%">MATIÈRES</th>
                <th class="center" style="width:6%">N/C</th>
                <th class="center" style="width:11%">Note de Comp. sur 20</th>
                <th class="center" style="width:8%">Coef.</th>
                <th class="center" style="width:10%">Moyenne</th>
                <th class="center" style="width:7%">Rang</th>
                <th style="width:18%">Observations</th>
                <th style="width:10%">Nom des professeurs</th>
                <th style="width:10%">Signature</th>
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
            $profName = trim(($mat['prof_prenom'] ?? '') . ' ' . ($mat['prof_nom'] ?? '')) ?: '—';
            $observation = '—';
            $rangMatiere = '—';
        ?>
        <tr>
            <td class="matiere"><?= htmlspecialchars($mat['matiere_nom']) ?></td>
            <td class="center"><?= $mat['nb_notes'] ?></td>
            <td class="center"><?= number_format($mat['moyenne'], 2) ?></td>
            <td class="center"><?= $mat['coefficient'] ?></td>
            <td class="center"><span class="<?= $cls ?>"><?= number_format($mat['moyenne'], 2) ?></span></td>
            <td class="center"><?= $rangMatiere ?></td>
            <td><?= htmlspecialchars($observation) ?></td>
            <td><?= htmlspecialchars($profName) ?></td>
            <td></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">Total coefficients : <?= $sumCoef ?></td>
                <td colspan="3" class="center">MOYENNE GÉNÉRALE</td>
                <td class="center" colspan="3" style="font-size:12pt">
                    <?= $sumCoef > 0 ? number_format($sumPond / $sumCoef, 2) : '—' ?> /20
                </td>
            </tr>
        </tfoot>
    </table>
    <?php endif; ?>

    <!-- RÉSULTATS ET MÉMO -->
    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-title">Moyenne générale</div>
            <div class="summary-value <?= couleurMoy($moyGen) ?>">
                <?= $moyGen > 0 ? number_format($moyGen, 2) : '—' ?> /20
            </div>
            <div class="summary-note">Mention : <?= htmlspecialchars($mention) ?></div>
        </div>
        <div class="summary-card">
            <div class="summary-title">Rang</div>
            <div class="summary-value"><?= $rang ?><sup>e</sup></div>
            <div class="summary-note">Sur <?= $totalEleves ?> élèves</div>
        </div>
        <div class="summary-card">
            <div class="summary-title">Absences</div>
            <div class="summary-value"><?= (int) $absenceStats['total'] ?> jours</div>
            <div class="summary-note">
                Justifiées : <?= (int) $absenceStats['justifiees'] ?> — Non justifiées : <?= (int) $absenceStats['non_justifiees'] ?>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-title">Moyennes annuelles</div>
            <div class="summary-value"><?= $moyenneAnnuelle > 0 ? number_format($moyenneAnnuelle, 2) : '—' ?> /20</div>
            <div class="summary-note">Décision : <?= htmlspecialchars($decision) ?></div>
        </div>
    </div>

    <div class="rappel-sections">
        <div class="rappel-card">
            <div class="rappel-title">Rappel des moyennes</div>
            <ul>
                <li>1er trimestre : <?= $periodeAverages[1] > 0 ? number_format($periodeAverages[1], 2) : '—' ?> /20</li>
                <li>2ème trimestre : <?= $periodeAverages[2] > 0 ? number_format($periodeAverages[2], 2) : '—' ?> /20</li>
                <li>3ème trimestre : <?= $periodeAverages[3] > 0 ? number_format($periodeAverages[3], 2) : '—' ?> /20</li>
            </ul>
        </div>
        <div class="rappel-card">
            <div class="rappel-title">Tableau d'honneur</div>
            <div class="rappel-text">______________</div>
            <div class="rappel-text">______________</div>
            <div class="rappel-text">______________</div>
        </div>
    </div>

    <div class="decision-box">
        <div class="decision-label">Décision du conseil :</div>
        <div class="decision-text"><?= htmlspecialchars($decision) ?></div>
    </div>

    <div class="appreciation">
        <div class="appreciation-label">Appréciation du Directeur / Conseil de classe :</div>
        <div style="height:45px"></div>
    </div>

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
