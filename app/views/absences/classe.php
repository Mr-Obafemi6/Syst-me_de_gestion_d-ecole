<!-- app/views/absences/classe.php -->

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Classe : <?= htmlspecialchars($classe['nom']) ?></h5>
        <span class="text-muted"><?= htmlspecialchars($classe['niveau']) ?></span>
    </div>
    <a href="<?= Router::url('absences') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Retour
    </a>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-bar-chart me-1 text-warning"></i>
        Bilan des absences — <?= htmlspecialchars($classe['nom']) ?>
    </div>
    <?php if (empty($stats)): ?>
    <div class="card-body text-center text-muted py-5">
        <p>Aucune donnée.</p>
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Élève</th>
                    <th>Matricule</th>
                    <th class="text-center">Total absences</th>
                    <th class="text-center">Justifiées</th>
                    <th class="text-center">Non justifiées</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($stats as $s): ?>
            <tr>
                <td class="fw-semibold"><?= htmlspecialchars($s['nom'] . ' ' . $s['prenom']) ?></td>
                <td class="text-muted small"><?= htmlspecialchars($s['matricule']) ?></td>
                <td class="text-center">
                    <?php if ($s['total_absences'] > 0): ?>
                    <span class="badge bg-warning text-dark"><?= $s['total_absences'] ?></span>
                    <?php else: ?>
                    <span class="text-success small">0</span>
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <span class="<?= $s['justifiees'] > 0 ? 'text-success fw-bold' : 'text-muted' ?>">
                        <?= $s['justifiees'] ?>
                    </span>
                </td>
                <td class="text-center">
                    <span class="<?= $s['non_justifiees'] > 0 ? 'text-danger fw-bold' : 'text-muted' ?>">
                        <?= $s['non_justifiees'] ?>
                    </span>
                </td>
                <td class="text-center">
                    <a href="<?= Router::url('absences/eleve/' . $s['id']) ?>"
                       class="btn btn-sm btn-outline-warning">
                        <i class="bi bi-eye"></i> Détail
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
