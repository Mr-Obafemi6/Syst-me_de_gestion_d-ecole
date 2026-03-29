<!-- app/views/classes/detail.php -->

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold text-primary mb-0"><?= htmlspecialchars($classe['nom']) ?></h4>
        <span class="text-muted"><?= htmlspecialchars($classe['niveau']) ?> &mdash; <?= htmlspecialchars($classe['annee_libelle']) ?></span>
    </div>
    <div class="d-flex gap-2">
        <?php if (AuthMiddleware::hasRole(ROLE_ADMIN)): ?>
        <a href="<?= Router::url('classes/ajouterMatiere/' . $classe['id']) ?>" class="btn btn-success btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Ajouter une matière
        </a>
        <a href="<?= Router::url('classes/modifier/' . $classe['id']) ?>" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil me-1"></i> Modifier
        </a>
        <?php endif; ?>
        <a href="<?= Router::url('classes') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Retour
        </a>
    </div>
</div>

<div class="row g-4">

    <!-- Matières -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-book-fill me-1 text-primary"></i> Matières</span>
                <span class="badge bg-primary"><?= count($matieres) ?> matière<?= count($matieres) > 1 ? 's' : '' ?></span>
            </div>

            <?php if (empty($matieres)): ?>
            <div class="card-body text-center text-muted py-4">
                <i class="bi bi-book fs-2"></i>
                <p class="mt-2 mb-0">Aucune matière. <a href="<?= Router::url('classes/ajouterMatiere/' . $classe['id']) ?>">Ajouter</a></p>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Matière</th>
                            <th class="text-center">Coefficient</th>
                            <th>Professeur</th>
                            <?php if (AuthMiddleware::hasRole(ROLE_ADMIN)): ?>
                            <th class="text-center">Action</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $totalCoef = 0;
                    foreach ($matieres as $mat):
                        $totalCoef += $mat['coefficient'];
                    ?>
                    <tr>
                        <td class="fw-semibold"><?= htmlspecialchars($mat['nom']) ?></td>
                        <td class="text-center">
                            <span class="badge bg-info text-dark"><?= $mat['coefficient'] ?></span>
                        </td>
                        <td>
                            <?php if ($mat['prof_nom']): ?>
                            <span class="text-success">
                                <i class="bi bi-person-check me-1"></i>
                                <?= htmlspecialchars($mat['prof_prenom'] . ' ' . $mat['prof_nom']) ?>
                            </span>
                            <?php else: ?>
                            <span class="text-muted fst-italic">Non assigné</span>
                            <?php endif; ?>
                        </td>
                        <?php if (AuthMiddleware::hasRole(ROLE_ADMIN)): ?>
                        <td class="text-center">
                            <form method="POST" action="<?= Router::url('classes/supprimerMatiere/' . $mat['id']) ?>" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                        data-confirm="Supprimer la matière <?= htmlspecialchars($mat['nom']) ?> ?">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td>Total coefficients</td>
                            <td class="text-center">
                                <span class="badge bg-primary"><?= $totalCoef ?></span>
                            </td>
                            <td colspan="<?= AuthMiddleware::hasRole(ROLE_ADMIN) ? 2 : 1 ?>"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Élèves -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-people-fill me-1 text-primary"></i> Élèves</span>
                <span class="badge bg-success"><?= count($eleves) ?> élève<?= count($eleves) > 1 ? 's' : '' ?></span>
            </div>

            <?php if (empty($eleves)): ?>
            <div class="card-body text-center text-muted py-4">
                <i class="bi bi-people fs-2"></i>
                <p class="mt-2 mb-0">Aucun élève dans cette classe.</p>
            </div>
            <?php else: ?>
            <div class="list-group list-group-flush" style="max-height:420px;overflow-y:auto">
                <?php foreach ($eleves as $eleve): ?>
                <a href="<?= Router::url('eleves/fiche/' . $eleve['id']) ?>"
                   class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-2">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                         style="width:32px;height:32px;font-size:.75rem;font-weight:700;flex-shrink:0">
                        <?= strtoupper(substr($eleve['prenom'],0,1) . substr($eleve['nom'],0,1)) ?>
                    </div>
                    <div class="flex-fill">
                        <div class="fw-semibold small"><?= htmlspecialchars($eleve['nom'] . ' ' . $eleve['prenom']) ?></div>
                        <div class="text-muted" style="font-size:.75rem"><?= htmlspecialchars($eleve['matricule']) ?></div>
                    </div>
                    <i class="bi bi-chevron-right text-muted small"></i>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="card-footer text-end">
                <a href="<?= Router::url('eleves?classe=' . $classe['id']) ?>" class="btn btn-sm btn-outline-primary">
                    Voir tous les élèves <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

</div>
