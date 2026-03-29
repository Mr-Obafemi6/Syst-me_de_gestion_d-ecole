<!-- app/views/eleves/fiche.php -->
<div class="row g-4">

    <!-- Colonne gauche : infos élève -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center py-4">
                <!-- Avatar -->
                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3"
                     style="width:80px;height:80px;font-size:2rem;font-weight:700">
                    <?= strtoupper(substr($eleve['prenom'],0,1) . substr($eleve['nom'],0,1)) ?>
                </div>
                <h5 class="fw-bold mb-1"><?= htmlspecialchars($eleve['prenom'] . ' ' . $eleve['nom']) ?></h5>
                <span class="badge bg-secondary font-monospace mb-2"><?= htmlspecialchars($eleve['matricule']) ?></span>
                <br>
                <?php if ($eleve['sexe'] === 'M'): ?>
                <span class="badge" style="background:#1a5276">♂ Masculin</span>
                <?php else: ?>
                <span class="badge" style="background:#7d3c98">♀ Féminin</span>
                <?php endif; ?>
            </div>
            <div class="card-footer p-0">
                <table class="table table-sm mb-0">
                    <tr>
                        <th class="ps-3">Classe</th>
                        <td><?= htmlspecialchars($eleve['classe_nom'] ?? '—') ?></td>
                    </tr>
                    <tr>
                        <th class="ps-3">Niveau</th>
                        <td><?= htmlspecialchars($eleve['classe_niveau'] ?? '—') ?></td>
                    </tr>
                    <tr>
                        <th class="ps-3">Naissance</th>
                        <td><?= $eleve['date_naissance'] ? date('d/m/Y', strtotime($eleve['date_naissance'])) : '—' ?></td>
                    </tr>
                    <tr>
                        <th class="ps-3">Inscrit le</th>
                        <td><?= date('d/m/Y', strtotime($eleve['created_at'])) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Parent -->
        <?php if ($eleve['parent_nom']): ?>
        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-person-heart me-1 text-primary"></i> Parent / Tuteur
            </div>
            <div class="card-body">
                <div class="fw-semibold"><?= htmlspecialchars($eleve['parent_prenom'] . ' ' . $eleve['parent_nom']) ?></div>
                <div class="text-muted small"><?= htmlspecialchars($eleve['parent_email']) ?></div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Actions admin -->
        <?php if (AuthMiddleware::hasRole(ROLE_ADMIN)): ?>
        <div class="card mt-3">
            <div class="card-body d-flex flex-column gap-2">
                <a href="<?= Router::url('eleves/modifier/' . $eleve['id']) ?>"
                   class="btn btn-warning btn-sm">
                    <i class="bi bi-pencil me-1"></i> Modifier
                </a>
                <form method="POST" action="<?= Router::url('eleves/supprimer/' . $eleve['id']) ?>">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100"
                            data-confirm="Supprimer définitivement cet élève ?">
                        <i class="bi bi-trash me-1"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Colonne droite : notes et paiements (à remplir phases 5 et 7) -->
    <div class="col-lg-8">

        <!-- Notes -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-pencil-square me-1 text-primary"></i> Notes & Bulletins</span>
            </div>
            <div class="card-body d-flex flex-column gap-2">
                <a href="<?= Router::url('notes/eleve/' . $eleve['id']) ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-bar-chart-fill me-1"></i> Relevé de notes
                </a>
                <?php foreach ([1=>'1er Trim.',2=>'2ème Trim.',3=>'3ème Trim.'] as $p => $lbl): ?>
                <a href="<?= Router::url('bulletins/eleve/' . $eleve['id'] . '?periode=' . $p) ?>"
                   class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-file-earmark-text me-1"></i> Bulletin <?= $lbl ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Paiements -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-cash-coin me-1 text-primary"></i> Paiements de scolarité</span>
            </div>
            <div class="card-body d-flex flex-column gap-2">
                <?php if (AuthMiddleware::hasRole(ROLE_ADMIN)): ?>
                <a href="<?= Router::url('paiements/ajouter?eleve=' . $eleve['id']) ?>"
                   class="btn btn-success btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Enregistrer un paiement
                </a>
                <?php endif; ?>
                <a href="<?= Router::url('paiements?eleve=' . $eleve['id']) ?>"
                   class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-receipt me-1"></i> Historique des paiements
                </a>
            </div>
        </div>

    </div>
</div>
