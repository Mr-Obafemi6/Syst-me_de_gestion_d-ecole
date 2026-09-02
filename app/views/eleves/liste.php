<!-- app/views/eleves/liste.php — v2 -->
<?php $p = $pagination; ?>

<!-- KPI Stats -->
<div class="row g-4 mb-4">
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Total élèves</span>
                <span class="kpi-icon-badge kpi-badge-blue"><i class="bi bi-people-fill"></i></span>
            </div>
            <div class="kpi-value"><?= $statsEleves['total'] ?></div>
            <div class="kpi-trend up"><i class="bi bi-check-circle"></i> actifs</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Garçons</span>
                <span class="kpi-icon-badge kpi-badge-teal"><i class="bi bi-gender-male"></i></span>
            </div>
            <div class="kpi-value"><?= $statsEleves['garcons'] ?></div>
            <div class="kpi-trend"><i class="bi bi-dot"></i> inscrits</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Filles</span>
                <span class="kpi-icon-badge kpi-badge-pink"><i class="bi bi-gender-female"></i></span>
            </div>
            <div class="kpi-value"><?= $statsEleves['filles'] ?></div>
            <div class="kpi-trend"><i class="bi bi-dot"></i> inscrites</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Nouveaux (30j)</span>
                <span class="kpi-icon-badge kpi-badge-purple"><i class="bi bi-person-plus-fill"></i></span>
            </div>
            <div class="kpi-value"><?= $statsEleves['nouveaux'] ?></div>
            <div class="kpi-trend up"><i class="bi bi-graph-up-arrow"></i> ce mois</div>
        </div>
    </div>
</div>

<!-- Barre d'outils -->
<div class="toolbar mb-4">
    <form method="GET" action="<?= Router::url('eleves') ?>" class="toolbar-filters">
        <div class="search-wrap">
            <i class="bi bi-search search-ico"></i>
            <input type="text" name="q" class="form-control search-input"
                   placeholder="Nom, prénom, matricule…"
                   value="<?= htmlspecialchars($p['recherche']) ?>">
        </div>
        <select name="classe" class="form-select" style="min-width:160px">
            <option value="">Toutes les classes</option>
            <?php foreach ($classes as $cl): ?>
            <option value="<?= $cl['id'] ?>" <?= $p['classe_id'] == $cl['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cl['nom']) ?>
            </option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-primary"><i class="bi bi-search me-1"></i> Rechercher</button>
        <?php if ($p['recherche'] || $p['classe_id']): ?>
        <a href="<?= Router::url('eleves') ?>" class="btn btn-outline-secondary" title="Réinitialiser">
            <i class="bi bi-x-lg"></i>
        </a>
        <?php endif; ?>
    </form>
    <?php if (AuthMiddleware::hasRole(ROLE_ADMIN)): ?>
    <a href="<?= Router::url('eleves/ajouter') ?>" class="btn btn-success ms-auto">
        <i class="bi bi-person-plus-fill me-1"></i> Ajouter un élève
    </a>
    <?php endif; ?>
</div>

<!-- Tableau -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people-fill me-2 text-primary"></i>Liste des élèves</span>
        <span class="badge bg-primary"><?= $p['total'] ?> élève<?= $p['total'] > 1 ? 's' : '' ?></span>
    </div>
    <div class="card-body p-0">
        <?php if (empty($p['data'])): ?>
        <div class="empty-state py-5">
            <i class="bi bi-people fs-1 text-muted"></i>
            <p class="mt-3 text-muted">Aucun élève trouvé.</p>
            <?php if ($p['recherche'] || $p['classe_id']): ?>
            <a href="<?= Router::url('eleves') ?>" class="btn btn-sm btn-outline-secondary mt-1">
                Réinitialiser les filtres
            </a>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Matricule</th>
                        <th>Élève</th>
                        <th class="text-center">Sexe</th>
                        <th>Classe</th>
                        <th>Naissance</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $av_colors = ['#2e6dbf','#7c3aed','#0891b2','#ea580c','#16a34a','#dc2626'];
                foreach ($p['data'] as $eleve):
                    $initials = strtoupper(substr($eleve['prenom'],0,1) . substr($eleve['nom'],0,1));
                    $av_bg = $av_colors[abs(crc32($eleve['nom'])) % count($av_colors)];
                ?>
                <tr class="eleve-row">
                    <td>
                        <span class="badge bg-light text-secondary font-monospace border">
                            <?= htmlspecialchars($eleve['matricule']) ?>
                        </span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-circle" style="background:<?= $av_bg ?>">
                                <?= $initials ?>
                            </div>
                            <div>
                                <div class="fw-semibold"><?= htmlspecialchars($eleve['nom'] . ' ' . $eleve['prenom']) ?></div>
                                <div class="text-muted" style="font-size:.75rem"><?= htmlspecialchars($eleve['classe_nom'] ?? '') ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <?php if ($eleve['sexe'] === 'M'): ?>
                        <span class="sexe-badge sexe-m">♂</span>
                        <?php else: ?>
                        <span class="sexe-badge sexe-f">♀</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="classe-pill"><?= htmlspecialchars($eleve['classe_nom'] ?? '—') ?></span>
                    </td>
                    <td class="text-muted small">
                        <?= $eleve['date_naissance'] ? date('d/m/Y', strtotime($eleve['date_naissance'])) : '—' ?>
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <a href="<?= Router::url('eleves/fiche/' . $eleve['id']) ?>"
                               class="btn btn-outline-primary" title="Voir la fiche">
                                <i class="bi bi-eye"></i>
                            </a>
                            <?php if (AuthMiddleware::hasRole(ROLE_ADMIN)): ?>
                            <a href="<?= Router::url('eleves/modifier/' . $eleve['id']) ?>"
                               class="btn btn-outline-warning" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="<?= Router::url('eleves/supprimer/' . $eleve['id']) ?>" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                <button type="submit" class="btn btn-outline-danger"
                                        data-confirm="Supprimer <?= htmlspecialchars($eleve['prenom'] . ' ' . $eleve['nom']) ?> ?"
                                        title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- Pagination intelligente -->
    <?php if ($p['last_page'] > 1): ?>
    <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
        <small class="text-muted">
            Page <strong><?= $p['current_page'] ?></strong> / <?= $p['last_page'] ?>
            &mdash; <strong><?= $p['total'] ?></strong> résultat<?= $p['total'] > 1 ? 's' : '' ?>
        </small>
        <nav>
            <ul class="pagination pagination-sm mb-0">
                <?php
                $cur   = $p['current_page'];
                $last  = $p['last_page'];
                $base  = Router::url('eleves?page=');
                $qs    = ($p['recherche'] ? '&q=' . urlencode($p['recherche']) : '')
                       . ($p['classe_id']  ? '&classe=' . $p['classe_id']     : '');

                // Prev
                echo '<li class="page-item ' . ($cur <= 1 ? 'disabled' : '') . '">';
                echo '<a class="page-link" href="' . ($cur > 1 ? $base . ($cur-1) . $qs : '#') . '"><i class="bi bi-chevron-left"></i></a></li>';

                // Pages avec ellipsis
                $pages = [];
                for ($i = 1; $i <= $last; $i++) {
                    if ($i === 1 || $i === $last || abs($i - $cur) <= 1) {
                        $pages[] = $i;
                    }
                }
                $prev = null;
                foreach ($pages as $pg) {
                    if ($prev !== null && $pg - $prev > 1) {
                        echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
                    }
                    echo '<li class="page-item ' . ($pg == $cur ? 'active' : '') . '">';
                    echo '<a class="page-link" href="' . $base . $pg . $qs . '">' . $pg . '</a></li>';
                    $prev = $pg;
                }

                // Next
                echo '<li class="page-item ' . ($cur >= $last ? 'disabled' : '') . '">';
                echo '<a class="page-link" href="' . ($cur < $last ? $base . ($cur+1) . $qs : '#') . '"><i class="bi bi-chevron-right"></i></a></li>';
                ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>
