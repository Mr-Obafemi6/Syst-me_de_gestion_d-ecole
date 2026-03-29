<!-- app/views/bulletins/classe.php -->

<?php $periodeLabel = ['','1er Trimestre','2ème Trimestre','3ème Trimestre'][$periode]; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold mb-0">Classe : <?= htmlspecialchars($classe['nom']) ?></h5>
        <span class="text-muted"><?= $periodeLabel ?> — <?= count($resultats) ?> élèves</span>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <?php foreach ([1=>'1er Trim.',2=>'2ème Trim.',3=>'3ème Trim.'] as $p => $lbl): ?>
        <a href="<?= Router::url('bulletins/classe/' . $classe['id'] . '?periode=' . $p) ?>"
           class="btn btn-sm <?= $periode == $p ? 'btn-primary' : 'btn-outline-secondary' ?>">
            <?= $lbl ?>
        </a>
        <?php endforeach; ?>
        <a href="<?= Router::url('bulletins/lot/' . $classe['id'] . '?periode=' . $periode) ?>"
           target="_blank" class="btn btn-sm btn-danger">
            <i class="bi bi-printer-fill me-1"></i> Imprimer tout
        </a>
        <a href="<?= Router::url('bulletins') ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Retour
        </a>
    </div>
</div>

<?php if (empty($resultats)): ?>
<div class="card">
    <div class="card-body text-center text-muted py-5">
        <i class="bi bi-pencil-square fs-1"></i>
        <p class="mt-3">Aucune note enregistrée pour cette classe ce trimestre.</p>
    </div>
</div>
<?php else: ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list-ol me-1 text-primary"></i> Classement — <?= $periodeLabel ?></span>
        <span class="badge bg-primary"><?= count($resultats) ?> élèves</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th class="text-center" style="width:60px">Rang</th>
                    <th>Élève</th>
                    <th>Matricule</th>
                    <th class="text-center">Moyenne /20</th>
                    <th class="text-center">Mention</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($resultats as $r): ?>
            <?php
                $moy = $r['moyenne'];
                $cls = $moy >= 14 ? 'success' : ($moy >= 10 ? 'primary' : ($moy >= 8 ? 'warning' : 'danger'));
                $medal = $r['rang'] == 1 ? '🥇' : ($r['rang'] == 2 ? '🥈' : ($r['rang'] == 3 ? '🥉' : ''));
            ?>
            <tr>
                <td class="text-center fw-bold fs-5">
                    <?= $medal ?: $r['rang'] ?>
                </td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                             style="width:32px;height:32px;font-size:.75rem;font-weight:700;flex-shrink:0">
                            <?= strtoupper(substr($r['eleve']['prenom'],0,1) . substr($r['eleve']['nom'],0,1)) ?>
                        </div>
                        <span class="fw-semibold">
                            <?= htmlspecialchars($r['eleve']['nom'] . ' ' . $r['eleve']['prenom']) ?>
                        </span>
                    </div>
                </td>
                <td class="text-muted small"><?= htmlspecialchars($r['eleve']['matricule']) ?></td>
                <td class="text-center">
                    <?php if ($moy > 0): ?>
                    <span class="badge bg-<?= $cls ?> fs-6"><?= number_format($moy, 2) ?></span>
                    <?php else: ?>
                    <span class="text-muted">—</span>
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <span class="badge <?= $moy >= 10 ? 'bg-success' : 'bg-danger' ?>">
                        <?= htmlspecialchars($r['mention']) ?>
                    </span>
                </td>
                <td class="text-center">
                    <a href="<?= Router::url('bulletins/eleve/' . $r['eleve']['id'] . '?periode=' . $periode) ?>"
                       class="btn btn-sm btn-outline-primary me-1">
                        <i class="bi bi-eye"></i> Voir
                    </a>
                    <a href="<?= Router::url('bulletins/eleve/' . $r['eleve']['id'] . '?periode=' . $periode . '&print=1') ?>"
                       target="_blank" class="btn btn-sm btn-danger">
                        <i class="bi bi-printer"></i> PDF
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
