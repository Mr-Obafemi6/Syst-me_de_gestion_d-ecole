<!-- app/views/espace_parent/index.php -->
<?php $resumes = $resumes ?? []; ?>

<?php if ($annee): ?>
<div class="annee-banner mb-4">
    <i class="bi bi-calendar3-fill"></i>
    <span>Année scolaire active : <strong><?= htmlspecialchars($annee['libelle']) ?></strong></span>
</div>
<?php endif; ?>

<?php if (empty($resumes)): ?>
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-emoji-neutral fs-1 text-muted d-block mb-3"></i>
        <p class="text-muted mb-0">Aucun enfant n'est encore lié à votre compte.<br>
        Contactez l'administration de l'établissement pour faire le lien.</p>
    </div>
</div>
<?php else: ?>

<div class="row g-4">
    <?php foreach ($resumes as $r): $enfant = $r['eleve']; ?>
    <div class="col-md-6 col-xl-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <?php
                    $colors = ['#2e6dbf','#7c3aed','#0891b2','#ea580c','#16a34a','#dc2626'];
                    $bg = $colors[abs(crc32($enfant['nom'])) % count($colors)];
                    ?>
                    <div class="avatar-circle" style="width:56px;height:56px;font-size:1.3rem;background:<?= $bg ?>">
                        <?= strtoupper(substr($enfant['prenom'],0,1) . substr($enfant['nom'],0,1)) ?>
                    </div>
                    <div>
                        <div class="fw-bold"><?= htmlspecialchars($enfant['prenom'] . ' ' . $enfant['nom']) ?></div>
                        <div class="text-muted small"><?= htmlspecialchars(($enfant['classe_nom'] ?? '—') . ' — ' . ($enfant['classe_niveau'] ?? '')) ?></div>
                    </div>
                </div>

                <div class="row text-center g-2 mb-3">
                    <div class="col-4">
                        <div class="fw-bold fs-5"><?= number_format($r['moyenne'], 2) ?></div>
                        <div class="text-muted small">Moyenne</div>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold fs-5 <?= $r['absences'] > 0 ? 'text-warning' : '' ?>"><?= (int) $r['absences'] ?></div>
                        <div class="text-muted small">Absences</div>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold fs-6"><?= number_format($r['total_paye'], 0, ',', ' ') ?></div>
                        <div class="text-muted small">FCFA payés</div>
                    </div>
                </div>

                <div class="d-flex flex-column gap-2">
                    <a href="<?= Router::url('eleves/fiche/' . $enfant['id']) ?>" class="btn btn-primary btn-sm">
                        <i class="bi bi-person-lines-fill me-1"></i> Fiche complète
                    </a>
                    <div class="d-flex gap-2">
                        <a href="<?= Router::url('notes/eleve/' . $enfant['id']) ?>" class="btn btn-outline-primary btn-sm flex-fill">
                            <i class="bi bi-bar-chart-fill me-1"></i> Notes
                        </a>
                        <a href="<?= Router::url('absences/eleve/' . $enfant['id']) ?>" class="btn btn-outline-warning btn-sm flex-fill">
                            <i class="bi bi-calendar-x me-1"></i> Absences
                        </a>
                    </div>
                    <a href="<?= Router::url('espace-parent/paiements/' . $enfant['id']) ?>" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-cash-coin me-1"></i> Paiements
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php endif; ?>
