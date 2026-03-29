<!-- app/views/eleves/liste.php -->
<?php $p = $pagination; ?>

<!-- Barre de recherche + actions -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <form method="GET" action="<?= Router::url('eleves') ?>" class="d-flex gap-2 flex-wrap">
        <input type="text" name="q" class="form-control" placeholder="Rechercher nom, prénom, matricule…"
               value="<?= htmlspecialchars($p['recherche']) ?>" style="min-width:220px">
        <select name="classe" class="form-select" style="min-width:160px">
            <option value="">Toutes les classes</option>
            <?php foreach ($classes as $cl): ?>
            <option value="<?= $cl['id'] ?>"
                <?= $p['classe_id'] == $cl['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cl['nom']) ?>
            </option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-primary"><i class="bi bi-search"></i> Rechercher</button>
        <?php if ($p['recherche'] || $p['classe_id']): ?>
        <a href="<?= Router::url('eleves') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-x"></i> Réinitialiser
        </a>
        <?php endif; ?>
    </form>

    <?php if (AuthMiddleware::hasRole(ROLE_ADMIN)): ?>
    <a href="<?= Router::url('eleves/ajouter') ?>" class="btn btn-success">
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
        <div class="text-center text-muted py-5">
            <i class="bi bi-people fs-1"></i>
            <p class="mt-2">Aucun élève trouvé.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Matricule</th>
                        <th>Nom & Prénom</th>
                        <th>Sexe</th>
                        <th>Classe</th>
                        <th>Date naissance</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($p['data'] as $eleve): ?>
                <tr>
                    <td>
                        <span class="badge bg-secondary font-monospace">
                            <?= htmlspecialchars($eleve['matricule']) ?>
                        </span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                 style="width:34px;height:34px;font-size:.8rem;font-weight:700;flex-shrink:0">
                                <?= strtoupper(substr($eleve['prenom'],0,1) . substr($eleve['nom'],0,1)) ?>
                            </div>
                            <div>
                                <div class="fw-semibold"><?= htmlspecialchars($eleve['nom'] . ' ' . $eleve['prenom']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <?php if ($eleve['sexe'] === 'M'): ?>
                        <span class="badge" style="background:#1a5276">♂ Masculin</span>
                        <?php else: ?>
                        <span class="badge" style="background:#7d3c98">♀ Féminin</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($eleve['classe_nom'] ?? '—') ?></td>
                    <td><?= $eleve['date_naissance'] ? date('d/m/Y', strtotime($eleve['date_naissance'])) : '—' ?></td>
                    <td class="text-center">
                        <a href="<?= Router::url('eleves/fiche/' . $eleve['id']) ?>"
                           class="btn btn-sm btn-outline-primary" title="Voir la fiche">
                            <i class="bi bi-eye"></i>
                        </a>
                        <?php if (AuthMiddleware::hasRole(ROLE_ADMIN)): ?>
                        <a href="<?= Router::url('eleves/modifier/' . $eleve['id']) ?>"
                           class="btn btn-sm btn-outline-warning" title="Modifier">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="<?= Router::url('eleves/supprimer/' . $eleve['id']) ?>"
                              class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    data-confirm="Supprimer <?= htmlspecialchars($eleve['prenom'] . ' ' . $eleve['nom']) ?> ?"
                                    title="Supprimer">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($p['last_page'] > 1): ?>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <small class="text-muted">
            Page <?= $p['current_page'] ?> / <?= $p['last_page'] ?>
            &mdash; <?= $p['total'] ?> résultat<?= $p['total'] > 1 ? 's' : '' ?>
        </small>
        <nav>
            <ul class="pagination pagination-sm mb-0">
                <?php for ($i = 1; $i <= $p['last_page']; $i++): ?>
                <li class="page-item <?= $i == $p['current_page'] ? 'active' : '' ?>">
                    <a class="page-link" href="<?= Router::url('eleves?page=' . $i
                        . ($p['recherche'] ? '&q=' . urlencode($p['recherche']) : '')
                        . ($p['classe_id'] ? '&classe=' . $p['classe_id'] : '')) ?>">
                        <?= $i ?>
                    </a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>
