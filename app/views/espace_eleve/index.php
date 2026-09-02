<!-- app/views/espace_eleve/index.php -->
<?php
$colors = ['#2e6dbf','#7c3aed','#0891b2','#ea580c','#16a34a','#dc2626'];
$bg = $colors[abs(crc32($eleve['nom'])) % count($colors)];
?>

<?php if ($annee): ?>
<div class="annee-banner mb-4">
    <i class="bi bi-calendar3-fill"></i>
    <span>Année scolaire active : <strong><?= htmlspecialchars($annee['libelle']) ?></strong></span>
</div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-body d-flex align-items-center gap-3 flex-wrap">
        <div class="avatar-circle" style="width:64px;height:64px;font-size:1.5rem;background:<?= $bg ?>">
            <?= strtoupper(substr($eleve['prenom'],0,1) . substr($eleve['nom'],0,1)) ?>
        </div>
        <div>
            <div class="fw-bold fs-4"><?= htmlspecialchars($eleve['prenom'] . ' ' . $eleve['nom']) ?></div>
            <div class="text-muted"><?= htmlspecialchars(($eleve['classe_nom'] ?? '—') . ' — ' . ($eleve['classe_niveau'] ?? '')) ?></div>
        </div>
        <span class="badge bg-secondary font-monospace ms-auto"><?= htmlspecialchars($eleve['matricule']) ?></span>
    </div>
</div>

<!-- KPI -->
<div class="row g-4 mb-4">
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Moyenne T1</span>
                <span class="kpi-icon-badge kpi-badge-blue"><i class="bi bi-bar-chart-fill"></i></span>
            </div>
            <div class="kpi-value"><?= number_format($moyGenerales[1] ?? 0, 2) ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Moyenne T2</span>
                <span class="kpi-icon-badge kpi-badge-purple"><i class="bi bi-bar-chart-fill"></i></span>
            </div>
            <div class="kpi-value"><?= number_format($moyGenerales[2] ?? 0, 2) ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Absences</span>
                <span class="kpi-icon-badge kpi-badge-pink"><i class="bi bi-calendar-x"></i></span>
            </div>
            <div class="kpi-value"><?= (int) ($stats['total'] ?? 0) ?></div>
            <div class="kpi-trend"><?= (int) ($stats['non_justifiees'] ?? 0) ?> non justifiées</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">FCFA payés</span>
                <span class="kpi-icon-badge kpi-badge-teal"><i class="bi bi-cash-coin"></i></span>
            </div>
            <div class="kpi-value" style="font-size:22px"><?= number_format($totalPaye, 0, ',', ' ') ?></div>
        </div>
    </div>
</div>

<!-- Accès rapide -->
<div class="row g-4">
    <div class="col-md-6 col-lg-3">
        <a href="<?= Router::url('notes/eleve/' . $eleve['id']) ?>" class="card text-decoration-none h-100">
            <div class="card-body text-center py-4">
                <i class="bi bi-pencil-square fs-1 text-primary d-block mb-2"></i>
                <div class="fw-semibold">Mes notes</div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="<?= Router::url('absences/eleve/' . $eleve['id']) ?>" class="card text-decoration-none h-100">
            <div class="card-body text-center py-4">
                <i class="bi bi-calendar-x fs-1 text-warning d-block mb-2"></i>
                <div class="fw-semibold">Mes absences</div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="<?= Router::url('espace-eleve/paiements') ?>" class="card text-decoration-none h-100">
            <div class="card-body text-center py-4">
                <i class="bi bi-cash-coin fs-1 text-success d-block mb-2"></i>
                <div class="fw-semibold">Mes paiements</div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="<?= Router::url('bulletins/eleve/' . $eleve['id']) ?>" class="card text-decoration-none h-100">
            <div class="card-body text-center py-4">
                <i class="bi bi-file-earmark-text fs-1 text-danger d-block mb-2"></i>
                <div class="fw-semibold">Mon bulletin</div>
            </div>
        </a>
    </div>
</div>
