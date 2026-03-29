<!-- app/views/bulletins/index.php -->

<div class="row justify-content-center">
<div class="col-lg-8">

<div class="card mb-4">
    <div class="card-header">
        <i class="bi bi-file-earmark-text me-1 text-primary"></i>
        Sélectionner une classe pour générer les bulletins
    </div>
    <div class="card-body">
        <?php if (empty($classes)): ?>
        <div class="text-center text-muted py-4">
            <i class="bi bi-building fs-2"></i>
            <p class="mt-2">Aucune classe active.</p>
        </div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($classes as $classe): ?>
            <div class="col-md-6">
                <div class="card border h-100">
                    <div class="card-body">
                        <h6 class="fw-bold text-primary"><?= htmlspecialchars($classe['nom']) ?></h6>
                        <div class="text-muted small mb-3">
                            <?= htmlspecialchars($classe['niveau']) ?> —
                            <?= $classe['nb_eleves'] ?> élève<?= $classe['nb_eleves'] > 1 ? 's' : '' ?>
                        </div>
                        <div class="d-flex gap-1 flex-wrap">
                            <?php foreach ([1 => '1er Trim.', 2 => '2ème Trim.', 3 => '3ème Trim.'] as $p => $label): ?>
                            <a href="<?= Router::url('bulletins/classe/' . $classe['id'] . '?periode=' . $p) ?>"
                               class="btn btn-sm btn-outline-primary">
                                <?= $label ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-info-circle me-1 text-primary"></i> Comment générer un bulletin ?
    </div>
    <div class="card-body">
        <ol class="mb-0">
            <li class="mb-1">Choisissez une classe et un trimestre ci-dessus pour voir tous les bulletins.</li>
            <li class="mb-1">Ou allez sur la <a href="<?= Router::url('eleves') ?>">fiche d'un élève</a> et cliquez <strong>"Bulletin"</strong>.</li>
            <li>Sur le bulletin, cliquez <strong>"Imprimer / Enregistrer PDF"</strong> puis choisissez <em>"Enregistrer en PDF"</em> dans la boîte de dialogue.</li>
        </ol>
    </div>
</div>

</div>
</div>
