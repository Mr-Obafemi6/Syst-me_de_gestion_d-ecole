<?php
$teacher = $teacher ?? [];
$assignments = $assignments ?? [];
$permissions = $permissions ?? [];
?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold text-primary mb-1">Fiche enseignant</h4>
        <div class="text-muted"><?= htmlspecialchars(($teacher['prenom'] ?? '') . ' ' . ($teacher['nom'] ?? '')) ?></div>
    </div>
    <div class="btn-group">
        <a href="<?= Router::url('enseignants') ?>" class="btn btn-outline-secondary">Retour</a>
        <a href="<?= Router::url('enseignants/ajouter') ?>" class="btn btn-primary">Ajouter</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-body text-center">
                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3" style="width:110px;height:110px; font-size:2rem; color:#0d6efd; font-weight:700;">
                    <?= htmlspecialchars(strtoupper(substr(($teacher['prenom'] ?? ''),0,1) . substr(($teacher['nom'] ?? ''),0,1))) ?>
                </div>
                <h5 class="mb-1"><?= htmlspecialchars(($teacher['prenom'] ?? '') . ' ' . ($teacher['nom'] ?? '')) ?></h5>
                <p class="text-muted mb-2"><?= htmlspecialchars($teacher['specialite'] ?? 'Spécialité non renseignée') ?></p>
                <span class="badge <?= !empty($teacher['compte_statut']) && $teacher['compte_statut'] === 'actif' ? 'bg-success' : 'bg-secondary' ?>">
                    <?= htmlspecialchars(($teacher['compte_statut'] ?? 'actif')) ?>
                </span>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Coordonnées</div>
            <div class="card-body small">
                <p class="mb-2"><strong>Matricule :</strong> <?= htmlspecialchars($teacher['matricule'] ?? '—') ?></p>
                <p class="mb-2"><strong>Email :</strong> <?= htmlspecialchars($teacher['email'] ?? '—') ?></p>
                <p class="mb-2"><strong>Téléphone :</strong> <?= htmlspecialchars($teacher['telephone'] ?? '—') ?></p>
                <p class="mb-2"><strong>Adresse :</strong> <?= nl2br(htmlspecialchars($teacher['adresse'] ?? '—')) ?></p>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">Affectations</div>
            <div class="card-body">
                <?php if (empty($assignments)): ?>
                    <div class="text-muted">Aucune affectation enregistrée.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Classe</th>
                                    <th>Matière</th>
                                    <th>Rôle</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($assignments as $assignment): ?>
                                <tr>
                                    <td><?= htmlspecialchars($assignment['classe'] ?? '—') ?></td>
                                    <td><?= htmlspecialchars($assignment['matiere'] ?? '—') ?></td>
                                    <td><?= htmlspecialchars($assignment['role'] ?? '—') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Permissions</div>
            <div class="card-body">
                <?php if (empty($permissions)): ?>
                    <div class="text-muted">Aucune permission spécifique.</div>
                <?php else: ?>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($permissions as $permission): ?>
                            <span class="badge bg-light text-dark border"><?= htmlspecialchars($permission['nom'] ?? '') ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
