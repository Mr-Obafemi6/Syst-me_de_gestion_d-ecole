<!-- app/views/absences/liste.php -->
<?php $p = $pagination; ?>

<!-- Absences du jour -->
<?php if (!empty($absencesAujourd)): ?>
<div class="alert alert-warning d-flex align-items-start gap-2 mb-4">
    <i class="bi bi-exclamation-triangle-fill fs-5 mt-1"></i>
    <div>
        <strong><?= count($absencesAujourd) ?> absence<?= count($absencesAujourd) > 1 ? 's' : '' ?> aujourd'hui :</strong>
        <?php foreach ($absencesAujourd as $a): ?>
        <span class="badge bg-warning text-dark ms-1">
            <?= htmlspecialchars($a['eleve_prenom'] . ' ' . $a['eleve_nom']) ?>
            (<?= htmlspecialchars($a['classe_nom']) ?>)
        </span>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Filtres + bouton ajouter -->
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <form method="GET" action="<?= Router::url('absences') ?>" class="d-flex gap-2 flex-wrap">
        <select name="classe" class="form-select form-select-sm" style="width:160px">
            <option value="">Toutes les classes</option>
            <?php foreach ($classes as $cl): ?>
            <option value="<?= $cl['id'] ?>" <?= $classeId == $cl['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cl['nom']) ?>
            </option>
            <?php endforeach; ?>
        </select>
        <select name="justifiee" class="form-select form-select-sm" style="width:160px">
            <option value="">Toutes</option>
            <option value="1" <?= $justifiee === '1' ? 'selected' : '' ?>>Justifiées</option>
            <option value="0" <?= $justifiee === '0' ? 'selected' : '' ?>>Non justifiées</option>
        </select>
        <button class="btn btn-sm btn-primary">
            <i class="bi bi-filter me-1"></i> Filtrer
        </button>
        <?php if ($classeId || $justifiee !== ''): ?>
        <a href="<?= Router::url('absences') ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-x"></i>
        </a>
        <?php endif; ?>
    </form>
    <a href="<?= Router::url('absences/ajouter') ?>" class="btn btn-warning">
        <i class="bi bi-plus-circle-fill me-1"></i> Enregistrer une absence
    </a>
</div>

<!-- Tableau -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-calendar-x me-1 text-warning"></i> Historique des absences</span>
        <span class="badge bg-warning text-dark"><?= $p['total'] ?> absence<?= $p['total'] > 1 ? 's' : '' ?></span>
    </div>
    <div class="card-body p-0">
        <?php if (empty($p['data'])): ?>
        <div class="text-center text-muted py-5">
            <i class="bi bi-calendar-check fs-1 text-success"></i>
            <p class="mt-2">Aucune absence enregistrée.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Élève</th>
                        <th>Classe</th>
                        <th>Date</th>
                        <th>Horaire</th>
                        <th class="text-center">Motif</th>
                        <th class="text-center">Statut</th>
                        <th>Commentaire</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($p['data'] as $abs):
                    $motifLabel = [
                        'maladie'       => ['label' => 'Maladie',      'cls' => 'info'],
                        'familial'      => ['label' => 'Familial',     'cls' => 'secondary'],
                        'non_justifie'  => ['label' => 'Non justifié', 'cls' => 'danger'],
                        'autre'         => ['label' => 'Autre',        'cls' => 'dark'],
                    ][$abs['motif']] ?? ['label' => $abs['motif'], 'cls' => 'secondary'];
                ?>
                <tr>
                    <td>
                        <a href="<?= Router::url('absences/eleve/' . $abs['eleve_id']) ?>"
                           class="fw-semibold text-decoration-none">
                            <?= htmlspecialchars($abs['eleve_nom'] . ' ' . $abs['eleve_prenom']) ?>
                        </a>
                        <div class="text-muted small"><?= htmlspecialchars($abs['matricule']) ?></div>
                    </td>
                    <td class="small"><?= htmlspecialchars($abs['classe_nom']) ?></td>
                    <td class="fw-semibold"><?= date('d/m/Y', strtotime($abs['date_absence'])) ?></td>
                    <td class="small text-muted">
                        <?php if ($abs['heure_debut'] && $abs['heure_fin']): ?>
                            <?= substr($abs['heure_debut'],0,5) ?> – <?= substr($abs['heure_fin'],0,5) ?>
                        <?php else: ?>
                            Journée
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-<?= $motifLabel['cls'] ?>">
                            <?= $motifLabel['label'] ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <?php if ($abs['justifiee']): ?>
                        <span class="badge bg-success"><i class="bi bi-check me-1"></i>Justifiée</span>
                        <?php else: ?>
                        <span class="badge bg-danger"><i class="bi bi-x me-1"></i>Non justifiée</span>
                        <?php endif; ?>
                    </td>
                    <td class="small text-muted">
                        <?= htmlspecialchars($abs['commentaire'] ?? '—') ?>
                    </td>
                    <td class="text-center">
                        <?php if (!$abs['justifiee']): ?>
                        <form method="POST"
                              action="<?= Router::url('absences/justifier/' . $abs['id']) ?>"
                              class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                            <button type="submit" class="btn btn-sm btn-outline-success"
                                    title="Marquer comme justifiée"
                                    data-confirm="Marquer cette absence comme justifiée ?">
                                <i class="bi bi-check-circle"></i>
                            </button>
                        </form>
                        <?php endif; ?>
                        <form method="POST"
                              action="<?= Router::url('absences/supprimer/' . $abs['id']) ?>"
                              class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    title="Supprimer"
                                    data-confirm="Supprimer cette absence ?">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
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
        <small class="text-muted">Page <?= $p['current_page'] ?> / <?= $p['last_page'] ?></small>
        <nav>
            <ul class="pagination pagination-sm mb-0">
                <?php for ($i = 1; $i <= $p['last_page']; $i++): ?>
                <li class="page-item <?= $i == $p['current_page'] ? 'active' : '' ?>">
                    <a class="page-link"
                       href="<?= Router::url('absences?page=' . $i
                           . ($classeId   ? '&classe='    . $classeId : '')
                           . ($justifiee !== '' ? '&justifiee=' . $justifiee : '')) ?>">
                        <?= $i ?>
                    </a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>
