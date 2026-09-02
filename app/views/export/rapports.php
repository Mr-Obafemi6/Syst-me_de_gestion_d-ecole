<!-- app/views/export/rapports.php -->
<?php
$classes = $classes ?? [];
$annee = $annee ?? null;
$reportTitle = $reportTitle ?? '';
$reportHeaders = $reportHeaders ?? [];
$reportRows = $reportRows ?? [];
$reportSummary = $reportSummary ?? '';
$selectedType = $selectedType ?? 'eleves';
$classeId = $classeId ?? 0;
$periode = $periode ?? 1;
?>

<div class="row g-4">
    <div class="col-12">
        <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
            <div>
                <h2 class="mb-1">Rapports</h2>
                <p class="text-muted mb-0">Visualisez et générez un rapport en PDF à partir des données scolaires.</p>
            </div>
            <div>
                <a href="<?= Router::url('export') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Retour aux exports
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-file-earmark-text-fill me-1 text-primary"></i> Choisir un rapport
            </div>
            <div class="card-body">
                <form method="GET" action="<?= Router::url('export/rapports') ?>">
                    <div class="mb-3">
                        <label class="form-label">Type de rapport</label>
                        <select name="type" class="form-select" onchange="this.form.submit()">
                            <option value="eleves" <?= $selectedType === 'eleves' ? 'selected' : '' ?>>Élèves actifs</option>
                            <option value="paiements" <?= $selectedType === 'paiements' ? 'selected' : '' ?>>Paiements</option>
                            <option value="inscriptions" <?= $selectedType === 'inscriptions' ? 'selected' : '' ?>>Inscriptions</option>
                        </select>
                    </div>

                    <?php if ($selectedType === 'eleves'): ?>
                    <div class="mb-3">
                        <label class="form-label">Filtrer par classe</label>
                        <select name="classe" class="form-select" onchange="this.form.submit()">
                            <option value="0">Toutes les classes</option>
                            <?php foreach ($classes as $cl): ?>
                            <option value="<?= $cl['id'] ?>" <?= $classeId === (int) $cl['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cl['nom']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <?php if ($selectedType === 'paiements'): ?>
                    <div class="mb-3">
                        <label class="form-label">Année scolaire</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($annee['libelle'] ?? 'N/A') ?>" disabled>
                    </div>
                    <?php endif; ?>

                    <?php if ($selectedType === 'inscriptions'): ?>
                    <div class="mb-3">
                        <label class="form-label">Période</label>
                        <select name="periode" class="form-select" onchange="this.form.submit()">
                            <option value="1" <?= $periode === 1 ? 'selected' : '' ?>>12 derniers mois</option>
                        </select>
                    </div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-eye-fill me-1"></i> Voir le rapport
                    </button>
                </form>
            </div>
            <div class="card-footer text-muted small">
                Le rapport est affiché dans un aperçu imprimable. Utilisez le bouton "PDF" pour générer le fichier.
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-file-earmark-pdf-fill me-1 text-danger"></i>
                    <?= htmlspecialchars($reportTitle ?: 'Aucun rapport sélectionné') ?>
                </div>
                <?php if (!empty($reportRows)): ?>
                <button type="button" class="btn btn-outline-secondary btn-sm print-report">
                    <i class="bi bi-printer me-1"></i> Imprimer / PDF
                </button>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (!empty($reportSummary)): ?>
                <div class="alert alert-info">
                    <?= htmlspecialchars($reportSummary) ?>
                </div>
                <?php endif; ?>

                <?php if (empty($reportRows)): ?>
                <div class="empty-state py-5 text-center text-muted">
                    <i class="bi bi-file-text fs-1"></i>
                    <p class="mt-3 mb-0">Sélectionnez un type de rapport et cliquez sur "Voir le rapport".</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped align-middle report-table">
                        <thead>
                            <tr>
                                <?php foreach ($reportHeaders as $header): ?>
                                <th><?= htmlspecialchars($header) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reportRows as $row): ?>
                            <tr>
                                <?php foreach ($row as $cell): ?>
                                <td><?= htmlspecialchars((string) $cell) ?></td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelector('.print-report')?.addEventListener('click', () => {
    window.print();
});
</script>
