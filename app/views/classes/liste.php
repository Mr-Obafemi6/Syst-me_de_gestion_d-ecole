<!-- app/views/classes/liste.php -->

<!-- KPI Stats -->
<div class="row g-4 mb-4">
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Classes actives</span>
                <span class="kpi-icon-badge kpi-badge-purple"><i class="bi bi-building"></i></span>
            </div>
            <div class="kpi-value"><?= $statsClasses['total'] ?></div>
            <div class="kpi-trend"><i class="bi bi-dot"></i> cette année</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Élèves répartis</span>
                <span class="kpi-icon-badge kpi-badge-blue"><i class="bi bi-people-fill"></i></span>
            </div>
            <div class="kpi-value"><?= $statsClasses['total_eleves'] ?></div>
            <div class="kpi-trend up"><i class="bi bi-check-circle"></i> affectés</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Moyenne / classe</span>
                <span class="kpi-icon-badge kpi-badge-teal"><i class="bi bi-bar-chart-fill"></i></span>
            </div>
            <div class="kpi-value"><?= $statsClasses['moyenne'] ?></div>
            <div class="kpi-trend"><i class="bi bi-dot"></i> élèves</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Matières couvertes</span>
                <span class="kpi-icon-badge kpi-badge-pink"><i class="bi bi-journal-bookmark-fill"></i></span>
            </div>
            <div class="kpi-value"><?= $statsClasses['total_matieres'] ?></div>
            <div class="kpi-trend"><i class="bi bi-dot"></i> au total</div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <?php if ($annee): ?>
        <span class="badge bg-primary fs-6 px-3 py-2">
            <i class="bi bi-calendar3 me-1"></i> Année scolaire : <?= htmlspecialchars($annee['libelle']) ?>
        </span>
        <?php endif; ?>
    </div>
    <?php if (AuthMiddleware::hasRole(ROLE_ADMIN)): ?>
    <a href="<?= Router::url('classes/ajouter') ?>" class="btn btn-success">
        <i class="bi bi-plus-circle-fill me-1"></i> Ajouter une classe
    </a>
    <?php endif; ?>
</div>

<?php if (empty($classes)): ?>
<div class="card">
    <div class="card-body text-center text-muted py-5">
        <i class="bi bi-building fs-1"></i>
        <p class="mt-3">Aucune classe trouvée pour l'année active.<br>
        <?php if (AuthMiddleware::hasRole(ROLE_ADMIN)): ?>
            <a href="<?= Router::url('classes/ajouter') ?>">Créer la première classe</a>
        <?php endif; ?>
        </p>
    </div>
</div>
<?php else: ?>

<?php foreach ($groupes as $cycleKey => $groupe): ?>
<h5 class="fw-bold text-secondary mb-3 mt-2">
    <i class="bi bi-<?= $cycleKey === 'lycee' ? 'mortarboard' : 'book' ?> me-2"></i>
    <?= htmlspecialchars($groupe['label']) ?>
</h5>
<div class="row g-4 mb-4">
    <?php foreach ($groupe['classes'] as $classe): ?>
    <div class="col-md-4 col-lg-3">
        <div class="card h-100 border-0 shadow-sm" style="border-top:4px solid #0f3460 !important;">
            <div class="card-body">
                <h5 class="fw-bold text-primary mb-1"><?= htmlspecialchars($classe['libelle_complete'] ?? $classe['nom']) ?></h5>
                <div class="text-muted small mb-3"><?= htmlspecialchars($classe['niveau_nom'] ?? $classe['niveau']) ?></div>

                <div class="d-flex gap-3 mb-3">
                    <div class="text-center">
                        <div class="fw-bold fs-4 text-primary"><?= $classe['nb_eleves'] ?></div>
                        <div class="small text-muted">Élève<?= $classe['nb_eleves'] > 1 ? 's' : '' ?></div>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold fs-4 text-success"><?= $classe['nb_matieres'] ?></div>
                        <div class="small text-muted">Matière<?= $classe['nb_matieres'] > 1 ? 's' : '' ?></div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent d-flex gap-2">
                <a href="<?= Router::url('classes/detail/' . $classe['id']) ?>"
                   class="btn btn-sm btn-primary flex-fill">
                    <i class="bi bi-eye me-1"></i> Détail
                </a>
                <?php if (AuthMiddleware::hasRole(ROLE_ADMIN)): ?>
                <a href="<?= Router::url('classes/modifier/' . $classe['id']) ?>"
                   class="btn btn-sm btn-outline-warning">
                    <i class="bi bi-pencil"></i>
                </a>
                <form method="POST" action="<?= Router::url('classes/supprimer/' . $classe['id']) ?>" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <button type="submit" class="btn btn-sm btn-outline-danger"
                            data-confirm="Supprimer la classe <?= htmlspecialchars($classe['libelle_complete'] ?? $classe['nom']) ?> ?">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endforeach; ?>

<?php endif; ?>
