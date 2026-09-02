<!-- app/views/notes/eleve.php -->

<!-- En-tête élève -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold mb-0"><?= htmlspecialchars($eleve['prenom'] . ' ' . $eleve['nom']) ?></h5>
        <span class="text-muted small">
            <?= htmlspecialchars($eleve['matricule']) ?> &mdash;
            <?= htmlspecialchars($eleve['classe_nom'] ?? '') ?>
        </span>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <!-- Filtre période -->
        <?php foreach ([0 => 'Toutes', 1 => '1er Trim.', 2 => '2ème Trim.', 3 => '3ème Trim.'] as $p => $label): ?>
        <a href="<?= Router::url('notes/eleve/' . $eleve['id'] . '?periode=' . $p) ?>"
           class="btn btn-sm <?= $periode == $p ? 'btn-primary' : 'btn-outline-secondary' ?>">
            <?= $label ?>
        </a>
        <?php endforeach; ?>
        <a href="<?= Router::url('eleves/fiche/' . $eleve['id']) ?>"
           class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Fiche élève
        </a>
    </div>
</div>

<!-- Moyennes générales par trimestre -->
<div class="row g-3 mb-4">
    <?php foreach ([1 => '1er Trim.', 2 => '2ème Trim.', 3 => '3ème Trim.'] as $p => $label): ?>
    <?php $moy = $moyGenerales[$p]; ?>
    <div class="col-md-4">
        <div class="card text-center py-3 <?= $moy >= 10 ? 'border-success' : ($moy > 0 ? 'border-danger' : '') ?>">
            <div class="fs-2 fw-bold <?= $moy >= 14 ? 'text-success' : ($moy >= 10 ? 'text-primary' : ($moy > 0 ? 'text-danger' : 'text-muted')) ?>">
                <?= $moy > 0 ? number_format($moy, 2) . '/20' : '—' ?>
            </div>
            <div class="text-muted small"><?= $label ?></div>
            <?php if ($moy > 0): ?>
            <div class="small mt-1 fw-semibold
                <?= $moy >= 14 ? 'text-success' : ($moy >= 12 ? 'text-info' : ($moy >= 10 ? 'text-primary' : 'text-danger')) ?>">
                <?= $moy >= 16 ? 'Très bien' : ($moy >= 14 ? 'Bien' : ($moy >= 12 ? 'Assez bien' : ($moy >= 10 ? 'Passable' : 'Insuffisant'))) ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Tableau des moyennes par matière -->
<?php if (empty($moyennes)): ?>
<div class="card">
    <div class="card-body text-center text-muted py-5">
        <i class="bi bi-pencil-square fs-1"></i>
        <p class="mt-3">Aucune note enregistrée<?= $periode > 0 ? ' pour ce trimestre' : '' ?>.</p>
    </div>
</div>
<?php else: ?>
<div class="card">
    <div class="card-header">
        <i class="bi bi-bar-chart-fill me-1 text-primary"></i>
        Moyennes par matière
        <?= $periode > 0 ? '— ' . ['', '1er Trimestre', '2ème Trimestre', '3ème Trimestre'][$periode] : '' ?>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Matière</th>
                    <th class="text-center">Coefficient</th>
                    <th class="text-center">Nb notes</th>
                    <th class="text-center">Min</th>
                    <th class="text-center">Max</th>
                    <th class="text-center">Moyenne</th>
                    <?php if ($periode == 0): ?>
                    <th class="text-center">Trimestre</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($moyennes as $moy): ?>
            <tr>
                <td class="fw-semibold"><?= htmlspecialchars($moy['matiere_nom']) ?></td>
                <td class="text-center">
                    <span class="badge bg-info text-dark"><?= $moy['coefficient'] ?></span>
                </td>
                <td class="text-center"><?= $moy['nb_notes'] ?></td>
                <td class="text-center text-danger"><?= number_format($moy['note_min'], 2) ?></td>
                <td class="text-center text-success"><?= number_format($moy['note_max'], 2) ?></td>
                <td class="text-center">
                    <?php
                    $m = $moy['moyenne'];
                    $cls = $m >= 14 ? 'success' : ($m >= 10 ? 'primary' : ($m >= 8 ? 'warning' : 'danger'));
                    ?>
                    <span class="badge bg-<?= $cls ?> fs-6"><?= number_format($m, 2) ?>/20</span>
                </td>
                <?php if ($periode == 0): ?>
                <td class="text-center">
                    <span class="badge bg-secondary">
                        <?= ['', '1er Trim.', '2ème Trim.', '3ème Trim.'][$moy['periode']] ?>
                    </span>
                </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Barre de progression visuelle par matière -->
<div class="card mt-4">
    <div class="card-header">
        <i class="bi bi-graph-up me-1 text-primary"></i> Progression par matière
    </div>
    <div class="card-body">
        <?php foreach ($moyennes as $moy):
            $pct = min(100, ($moy['moyenne'] / 20) * 100);
            $cls = $moy['moyenne'] >= 14 ? 'success' : ($moy['moyenne'] >= 10 ? 'primary' : ($moy['moyenne'] >= 8 ? 'warning' : 'danger'));
        ?>
        <div class="mb-3">
            <div class="d-flex justify-content-between mb-1">
                <span class="small fw-semibold"><?= htmlspecialchars($moy['matiere_nom']) ?></span>
                <span class="small text-<?= $cls ?> fw-bold"><?= number_format($moy['moyenne'], 2) ?>/20</span>
            </div>
            <div class="progress" style="height:10px">
                <div class="progress-bar bg-<?= $cls ?>" style="width:<?= $pct ?>%"></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>
