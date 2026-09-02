<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold text-primary mb-0">Gestion des matières</h4>
        <div class="text-muted small">Collège (6ème → 3ème) et Lycée (2nde → Terminale) — matières par classe</div>
    </div>
    <?php if (AuthMiddleware::hasRole(ROLE_ADMIN)): ?>
        <div class="d-flex gap-2">
            <a href="<?= Router::url('matieres/ajouter') ?>" class="btn btn-outline-primary">
                <i class="bi bi-journal-plus me-1"></i> Catalogue
            </a>
            <a href="<?= Router::url('classes/ajouter') ?>" class="btn btn-success">
                <i class="bi bi-building-add me-1"></i> Nouvelle classe
            </a>
        </div>
    <?php endif; ?>
</div>

<?php if (empty($groupes)): ?>
<div class="card shadow-sm border-0">
    <div class="card-body text-center text-muted py-5">
        <i class="bi bi-building fs-1"></i>
        <p class="mt-3 mb-0">Aucune classe active pour l'année en cours.<br>
        <?php if (AuthMiddleware::hasRole(ROLE_ADMIN)): ?>
            <a href="<?= Router::url('classes/ajouter') ?>">Créer une classe</a>
        <?php endif; ?>
        </p>
    </div>
</div>
<?php else: ?>

<?php foreach ($groupes as $cycleKey => $groupe): ?>
<section class="mb-5">
    <h5 class="fw-bold text-secondary mb-3">
        <i class="bi bi-<?= $cycleKey === 'lycee' ? 'mortarboard' : 'book' ?> me-2"></i>
        <?= htmlspecialchars($groupe['label']) ?>
    </h5>

    <div class="row g-4">
        <?php foreach ($groupe['classes'] as $classe): ?>
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 bg-white">
                    <div>
                        <span class="fw-bold text-primary fs-5"><?= htmlspecialchars($classe['libelle_complete']) ?></span>
                        <span class="text-muted ms-2 small">
                            <?= (int) ($classe['nb_eleves'] ?? 0) ?> élève(s) ·
                            <?= count($classe['matieres']) ?> matière(s)
                        </span>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="<?= Router::url('classes/detail/' . $classe['classe_id']) ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye me-1"></i> Détail
                        </a>
                        <?php if (AuthMiddleware::hasRole(ROLE_ADMIN)): ?>
                        <a href="<?= Router::url('classes/ajouterMatiere/' . $classe['classe_id']) ?>" class="btn btn-sm btn-success">
                            <i class="bi bi-plus-circle me-1"></i> Ajouter
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (empty($classe['matieres'])): ?>
                <div class="card-body text-muted text-center py-4">
                    Aucune matière assignée à cette classe.
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Matière</th>
                                <th>Code</th>
                                <th class="text-center">Coef.</th>
                                <th>Professeur</th>
                                <?php if (AuthMiddleware::hasRole(ROLE_ADMIN)): ?>
                                <th class="text-center" style="width:120px">Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totalCoef = 0;
                            foreach ($classe['matieres'] as $mat):
                                $totalCoef += (float) $mat['coefficient'];
                            ?>
                            <tr>
                                <td class="fw-semibold"><?= htmlspecialchars($mat['nom']) ?></td>
                                <td><?= htmlspecialchars($mat['code'] ?? '—') ?></td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary"><?= number_format((float) $mat['coefficient'], 1, ',', ' ') ?></span>
                                </td>
                                <td>
                                    <?php if (!empty($mat['prof_nom'])): ?>
                                        <?= htmlspecialchars($mat['prof_prenom'] . ' ' . $mat['prof_nom']) ?>
                                    <?php else: ?>
                                        <span class="text-muted fst-italic">Non assigné</span>
                                    <?php endif; ?>
                                </td>
                                <?php if (AuthMiddleware::hasRole(ROLE_ADMIN)): ?>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="<?= Router::url('matieres/modifier/' . $mat['id']) ?>" class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="<?= Router::url('matieres/retirer') ?>" class="d-inline">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                            <input type="hidden" name="classe_id" value="<?= (int) $classe['classe_id'] ?>">
                                            <input type="hidden" name="matiere_id" value="<?= (int) $mat['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    data-confirm="Retirer <?= htmlspecialchars($mat['nom']) ?> de <?= htmlspecialchars($classe['libelle_complete']) ?> ?"
                                                    title="Retirer de la classe">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td colspan="2">Total coefficients</td>
                                <td class="text-center"><span class="badge bg-primary"><?= number_format($totalCoef, 1, ',', ' ') ?></span></td>
                                <td colspan="<?= AuthMiddleware::hasRole(ROLE_ADMIN) ? 2 : 1 ?>"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?php endif; ?>

                <?php if (AuthMiddleware::hasRole(ROLE_ADMIN) && !empty($catalogue)): ?>
                <div class="card-footer bg-light">
                    <form method="POST" action="<?= Router::url('matieres/assigner') ?>" class="row g-2 align-items-end">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                        <input type="hidden" name="classe_id" value="<?= (int) $classe['classe_id'] ?>">
                        <div class="col-md-6">
                            <label class="form-label small mb-1">Assigner une matière du catalogue</label>
                            <select name="matiere_id" class="form-select form-select-sm" required>
                                <option value="">— Choisir —</option>
                                <?php foreach ($catalogue as $m): ?>
                                <option value="<?= (int) $m['id'] ?>"><?= htmlspecialchars($m['nom']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small mb-1">Coef.</label>
                            <input type="number" name="coefficient" class="form-control form-control-sm" min="0.5" max="20" step="0.5" value="1" required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-sm btn-primary w-100">
                                <i class="bi bi-link-45deg me-1"></i> Assigner à la classe
                            </button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endforeach; ?>

<?php endif; ?>
