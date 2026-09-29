<?php
$teachers = $teachers ?? [];
$q = trim((string) ($q ?? ''));
$statut = trim((string) ($statut ?? ''));
$csrf_token = (string) ($csrf_token ?? '');
?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold text-primary mb-1">Enseignants</h4>
        <div class="text-muted">Gestion des enseignants, comptes et permissions</div>
    </div>
    <a href="<?= Router::url('enseignants/ajouter') ?>" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i> Ajouter un enseignant
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= Router::url('enseignants') ?>" class="row g-2 align-items-end">
            <div class="col-md-6">
                <label class="form-label">Recherche</label>
                <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" class="form-control" placeholder="Nom, prénom, email, téléphone, matricule">
            </div>
            <div class="col-md-3">
                <label class="form-label">Statut</label>
                <select name="statut" class="form-select">
                    <option value="">Tous</option>
                    <option value="actif" <?= $statut === 'actif' ? 'selected' : '' ?>>Actif</option>
                    <option value="bloque" <?= $statut === 'bloque' ? 'selected' : '' ?>>Bloqué</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-outline-primary w-100">Filtrer</button>
                <a href="<?= Router::url('enseignants') ?>" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Enseignant</th>
                    <th>Matricule</th>
                    <th>Contact</th>
                    <th>Statut</th>
                    <th>Compte</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($teachers as $teacher): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center" style="width:38px;height:38px; font-weight:700;">
                                <?= htmlspecialchars(strtoupper(substr(($teacher['prenom'] ?? ''), 0, 1) . substr(($teacher['nom'] ?? ''), 0, 1))) ?>
                            </div>
                            <div>
                                <div class="fw-semibold"><?= htmlspecialchars(($teacher['prenom'] ?? '') . ' ' . ($teacher['nom'] ?? '')) ?></div>
                                <small class="text-muted"><?= htmlspecialchars($teacher['specialite'] ?? '') ?></small>
                            </div>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($teacher['matricule'] ?? '—') ?></td>
                    <td>
                        <div><?= htmlspecialchars($teacher['email'] ?? '') ?></div>
                        <small><?= htmlspecialchars($teacher['telephone'] ?? '') ?></small>
                    </td>
                    <td>
                        <?php if (!empty($teacher['actif']) && ($teacher['actif'] == 1)): ?>
                            <span class="badge bg-success">Actif</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Bloqué</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($teacher['actif']) && $teacher['actif'] == 1): ?>
                            <span class="text-success">Ouvert</span>
                        <?php else: ?>
                            <span class="text-danger">Fermé</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <div class="btn-group">
                            <a href="<?= Router::url('enseignants/fiche/' . (int) $teacher['id']) ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                            <?php if ((int) $teacher['actif'] === 1): ?>
                                <form method="POST" action="<?= Router::url('enseignants/bloquer/' . (int) $teacher['user_id']) ?>" class="d-inline" onsubmit="return confirm('Bloquer ce compte enseignant ?');">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                    <button class="btn btn-sm btn-outline-warning"><i class="bi bi-lock"></i></button>
                                </form>
                            <?php else: ?>
                                <form method="POST" action="<?= Router::url('enseignants/activer/' . (int) $teacher['user_id']) ?>" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                    <button class="btn btn-sm btn-outline-success"><i class="bi bi-unlock"></i></button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
