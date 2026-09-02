<!-- app/views/recherche/index.php -->

<!-- Barre de recherche principale -->
<div class="row justify-content-center mb-4">
<div class="col-lg-8">
<form method="GET" action="<?= Router::url('recherche') ?>">
    <div class="input-group input-group-lg shadow-sm">
        <span class="input-group-text bg-white">
            <i class="bi bi-search text-primary"></i>
        </span>
        <input type="text" name="q" class="form-control border-start-0"
               value="<?= htmlspecialchars($q) ?>"
               placeholder="Rechercher un élève, une classe, un reçu..."
               autofocus autocomplete="off">
        <button type="submit" class="btn btn-primary px-4">Rechercher</button>
        <?php if ($q): ?>
        <a href="<?= Router::url('recherche') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-x"></i>
        </a>
        <?php endif; ?>
    </div>
</form>
</div>
</div>

<?php if ($resultats === null): ?>
<!-- État initial -->
<div class="text-center text-muted py-5">
    <i class="bi bi-search fs-1"></i>
    <p class="mt-3 fs-5">Tapez au moins 2 caractères pour rechercher.</p>
    <p class="small">Vous pouvez rechercher par nom, prénom, matricule, numéro de reçu...</p>
</div>

<?php elseif ($resultats['total'] === 0): ?>
<!-- Aucun résultat -->
<div class="text-center text-muted py-5">
    <i class="bi bi-emoji-frown fs-1"></i>
    <p class="mt-3 fs-5">Aucun résultat pour <strong>"<?= htmlspecialchars($q) ?>"</strong></p>
</div>

<?php else: ?>
<!-- Résultats -->
<div class="mb-3 text-muted">
    <strong><?= $resultats['total'] ?></strong> résultat<?= $resultats['total'] > 1 ? 's' : '' ?>
    pour <strong>"<?= htmlspecialchars($q) ?>"</strong>
</div>

<div class="row g-4">

    <!-- Élèves -->
    <?php if (!empty($resultats['eleves'])): ?>
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-people-fill me-1 text-primary"></i>
                Élèves (<?= count($resultats['eleves']) ?>)
            </div>
            <div class="list-group list-group-flush">
                <?php foreach ($resultats['eleves'] as $el): ?>
                <a href="<?= Router::url('eleves/fiche/' . $el['id']) ?>"
                   class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-2">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                         style="width:36px;height:36px;font-size:.8rem;font-weight:700;flex-shrink:0">
                        <?= strtoupper(substr($el['prenom'],0,1).substr($el['nom'],0,1)) ?>
                    </div>
                    <div class="flex-fill">
                        <div class="fw-semibold">
                            <?= htmlspecialchars($el['prenom'] . ' ' . $el['nom']) ?>
                        </div>
                        <div class="text-muted small">
                            <?= htmlspecialchars($el['matricule']) ?>
                            <?= $el['classe_nom'] ? ' — ' . htmlspecialchars($el['classe_nom']) : '' ?>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Classes -->
    <?php if (!empty($resultats['classes'])): ?>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-building me-1 text-success"></i>
                Classes (<?= count($resultats['classes']) ?>)
            </div>
            <div class="list-group list-group-flush">
                <?php foreach ($resultats['classes'] as $cl): ?>
                <a href="<?= Router::url('classes/detail/' . $cl['id']) ?>"
                   class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-2">
                    <i class="bi bi-building text-success"></i>
                    <div class="flex-fill">
                        <div class="fw-semibold"><?= htmlspecialchars($cl['nom']) ?></div>
                        <div class="text-muted small">
                            <?= htmlspecialchars($cl['niveau']) ?> —
                            <?= $cl['nb_eleves'] ?> élève<?= $cl['nb_eleves'] > 1 ? 's' : '' ?>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Paiements -->
    <?php if (!empty($resultats['paiements'])): ?>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-receipt me-1 text-warning"></i>
                Paiements (<?= count($resultats['paiements']) ?>)
            </div>
            <div class="list-group list-group-flush">
                <?php foreach ($resultats['paiements'] as $pa): ?>
                <a href="<?= Router::url('paiements/recu/' . $pa['id']) ?>"
                   class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-2">
                    <i class="bi bi-receipt text-warning"></i>
                    <div class="flex-fill">
                        <div class="fw-semibold font-monospace"><?= htmlspecialchars($pa['recu_numero']) ?></div>
                        <div class="text-muted small">
                            <?= htmlspecialchars($pa['eleve_prenom'] . ' ' . $pa['eleve_nom']) ?> —
                            <?= number_format($pa['montant_fcfa'], 0, ',', ' ') ?> FCFA
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Utilisateurs (admin seulement) -->
    <?php if (!empty($resultats['users']) && AuthMiddleware::hasRole(ROLE_ADMIN)): ?>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person-badge me-1 text-secondary"></i>
                Utilisateurs (<?= count($resultats['users']) ?>)
            </div>
            <div class="list-group list-group-flush">
                <?php foreach ($resultats['users'] as $u):
                    $roleCls = ['admin'=>'primary','professeur'=>'success','parent'=>'secondary'];
                ?>
                <a href="<?= Router::url('parametres#tab-users') ?>"
                   class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-2">
                    <i class="bi bi-person-circle text-secondary fs-5"></i>
                    <div class="flex-fill">
                        <div class="fw-semibold">
                            <?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?>
                            <span class="badge bg-<?= $roleCls[$u['role']] ?? 'secondary' ?> ms-1">
                                <?= ucfirst($u['role']) ?>
                            </span>
                        </div>
                        <div class="text-muted small"><?= htmlspecialchars($u['email']) ?></div>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>
<?php endif; ?>
