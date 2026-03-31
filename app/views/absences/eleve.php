<!-- app/views/absences/eleve.php -->

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold mb-0"><?= htmlspecialchars($eleve['prenom'] . ' ' . $eleve['nom']) ?></h5>
        <span class="text-muted small"><?= htmlspecialchars($eleve['matricule']) ?> — <?= htmlspecialchars($eleve['classe_nom'] ?? '') ?></span>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= Router::url('absences/ajouter?eleve=' . $eleve['id']) ?>"
           class="btn btn-sm btn-warning">
            <i class="bi bi-plus-circle me-1"></i> Ajouter une absence
        </a>
        <a href="<?= Router::url('eleves/fiche/' . $eleve['id']) ?>"
           class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Fiche élève
        </a>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card text-center py-3 border-warning">
            <div class="fs-2 fw-bold text-warning"><?= $stats['total'] ?></div>
            <div class="text-muted small">Total absences</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center py-3 border-success">
            <div class="fs-2 fw-bold text-success"><?= $stats['justifiees'] ?></div>
            <div class="text-muted small">Justifiées</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center py-3 border-danger">
            <div class="fs-2 fw-bold text-danger"><?= $stats['non_justifiees'] ?></div>
            <div class="text-muted small">Non justifiées</div>
        </div>
    </div>
</div>

<!-- Tableau -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-calendar-x me-1 text-warning"></i> Historique des absences
    </div>
    <?php if (empty($absences)): ?>
    <div class="card-body text-center text-muted py-5">
        <i class="bi bi-calendar-check fs-2 text-success"></i>
        <p class="mt-2">Aucune absence enregistrée pour cet élève.</p>
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Horaire</th>
                    <th class="text-center">Motif</th>
                    <th class="text-center">Statut</th>
                    <th>Commentaire</th>
                    <th>Saisi par</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($absences as $abs):
                $motifLabel = [
                    'maladie'      => ['label' => 'Maladie',      'cls' => 'info'],
                    'familial'     => ['label' => 'Familial',     'cls' => 'secondary'],
                    'non_justifie' => ['label' => 'Non justifié', 'cls' => 'danger'],
                    'autre'        => ['label' => 'Autre',        'cls' => 'dark'],
                ][$abs['motif']] ?? ['label' => $abs['motif'], 'cls' => 'secondary'];
            ?>
            <tr>
                <td class="fw-semibold"><?= date('d/m/Y', strtotime($abs['date_absence'])) ?></td>
                <td class="small text-muted">
                    <?= ($abs['heure_debut'] && $abs['heure_fin'])
                        ? substr($abs['heure_debut'],0,5) . ' – ' . substr($abs['heure_fin'],0,5)
                        : 'Journée' ?>
                </td>
                <td class="text-center">
                    <span class="badge bg-<?= $motifLabel['cls'] ?>"><?= $motifLabel['label'] ?></span>
                </td>
                <td class="text-center">
                    <?php if ($abs['justifiee']): ?>
                    <span class="badge bg-success">✓ Justifiée</span>
                    <?php else: ?>
                    <span class="badge bg-danger">✗ Non justifiée</span>
                    <?php endif; ?>
                </td>
                <td class="small"><?= htmlspecialchars($abs['commentaire'] ?? '—') ?></td>
                <td class="small text-muted">
                    <?= htmlspecialchars($abs['saisie_par_prenom'] . ' ' . $abs['saisie_par_nom']) ?>
                </td>
                <td class="text-center">
                    <?php if (!$abs['justifiee']): ?>
                    <form method="POST" action="<?= Router::url('absences/justifier/' . $abs['id']) ?>" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                        <button type="submit" class="btn btn-sm btn-outline-success"
                                title="Justifier"
                                data-confirm="Marquer comme justifiée ?">
                            <i class="bi bi-check-circle"></i>
                        </button>
                    </form>
                    <?php endif; ?>
                    <form method="POST" action="<?= Router::url('absences/supprimer/' . $abs['id']) ?>" class="d-inline">
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
