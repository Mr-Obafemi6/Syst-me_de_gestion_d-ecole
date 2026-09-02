<!-- app/views/eleves/fiche.php -->
<div class="row g-4">

    <!-- Colonne gauche : infos élève -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center py-4">
                <!-- Avatar -->
                <?php
                $fiche_colors = ['#2e6dbf','#7c3aed','#0891b2','#ea580c','#16a34a','#dc2626'];
                $fiche_bg = $fiche_colors[abs(crc32($eleve['nom'])) % count($fiche_colors)];
                ?>
                <div class="avatar-circle mb-3" style="width:80px;height:80px;font-size:2rem;background:<?= $fiche_bg ?>">
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
        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-person-heart me-1 text-primary"></i> Parent / Tuteur
            </div>
            <div class="card-body">
                <?php if (!empty($eleve['parent_nom'])): ?>
                <div class="fw-semibold"><?= htmlspecialchars($eleve['parent_prenom'] . ' ' . $eleve['parent_nom']) ?></div>
                <div class="text-muted small"><?= htmlspecialchars($eleve['parent_email']) ?></div>
                <?php if (!empty($eleve['parent_telephone'])): ?>
                <div class="text-muted small"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($eleve['parent_telephone']) ?></div>
                <?php endif; ?>
                <?php else: ?>
                <div class="text-muted small mb-2">Aucun parent lié à cet élève.</div>
                <?php if (AuthMiddleware::hasRole(ROLE_ADMIN)): ?>
                <a href="<?= Router::url('eleves/modifier/' . $eleve['id']) ?>" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-person-plus me-1"></i> Lier / créer un compte parent
                </a>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Compte de connexion élève -->
        <?php if (AuthMiddleware::hasRole(ROLE_ADMIN)): ?>
        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-key-fill me-1 text-primary"></i> Compte de connexion élève
            </div>
            <div class="card-body">
                <?php if (!empty($eleve['user_id'])): ?>
                <div class="fw-semibold small">
                    <span class="badge bg-<?= !empty($eleve['compte_actif']) ? 'success' : 'secondary' ?>">
                        <?= !empty($eleve['compte_actif']) ? 'Actif' : 'Désactivé' ?>
                    </span>
                </div>
                <div class="text-muted small mt-1 mb-2"><?= htmlspecialchars($eleve['compte_email'] ?? '') ?></div>
                <form method="POST" action="<?= Router::url('eleves/creerCompte/' . $eleve['id']) ?>" class="mb-2">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                    <button type="submit" class="btn btn-outline-warning btn-sm w-100">
                        <i class="bi bi-arrow-repeat me-1"></i> Réinitialiser le mot de passe
                    </button>
                </form>
                <?php if (!empty($eleve['compte_actif'])): ?>
                <form method="POST" action="<?= Router::url('eleves/desactiverCompte/' . $eleve['id']) ?>">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100"
                            data-confirm="Désactiver l'accès de l'élève à son espace ?">
                        <i class="bi bi-lock me-1"></i> Désactiver l'accès
                    </button>
                </form>
                <?php endif; ?>
                <?php else: ?>
                <p class="text-muted small">Cet élève n'a pas encore de compte personnel pour consulter ses notes, absences et paiements en ligne.</p>
                <form method="POST" action="<?= Router::url('eleves/creerCompte/' . $eleve['id']) ?>">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                    <div class="mb-2">
                        <label class="form-label small mb-1">Identifiant (email) — optionnel</label>
                        <input type="text" name="compte_email" class="form-control form-control-sm"
                               placeholder="<?= htmlspecialchars($eleve['matricule']) ?>@sge.local (auto si vide)">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-person-badge me-1"></i> Créer le compte élève
                    </button>
                </form>
                <?php endif; ?>
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

    <!-- Colonne droite : notes, absences, paiements -->
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

        <!-- Absences -->
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-calendar-x text-warning"></i> Absences
            </div>
            <div class="card-body d-flex flex-column gap-2">
                <?php if (AuthMiddleware::hasRole(ROLE_ADMIN) || AuthMiddleware::hasRole(ROLE_PROF)): ?>
                <a href="<?= Router::url('absences/ajouter?eleve=' . $eleve['id']) ?>"
                   class="btn btn-warning btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Enregistrer une absence
                </a>
                <?php endif; ?>
                <a href="<?= Router::url('absences/eleve/' . $eleve['id']) ?>"
                   class="btn btn-outline-warning btn-sm">
                    <i class="bi bi-calendar-x me-1"></i> Voir les absences
                </a>
            </div>
        </div>

        <!-- Paiements -->
        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-cash-coin text-success"></i> Paiements
            </div>
            <div class="card-body d-flex flex-column gap-2">
                <?php if (AuthMiddleware::hasRole(ROLE_ADMIN)): ?>
                <a href="<?= Router::url('paiements/ajouter?eleve=' . $eleve['id']) ?>"
                   class="btn btn-success btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Enregistrer un paiement
                </a>
                <a href="<?= Router::url('paiements?eleve=' . $eleve['id']) ?>"
                   class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-receipt me-1"></i> Historique des paiements
                </a>
                <?php elseif (AuthMiddleware::hasRole(ROLE_PARENT)): ?>
                <a href="<?= Router::url('espace-parent/paiements/' . $eleve['id']) ?>"
                   class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-receipt me-1"></i> Historique des paiements
                </a>
                <?php elseif (AuthMiddleware::hasRole(ROLE_ELEVE)): ?>
                <a href="<?= Router::url('espace-eleve/paiements') ?>"
                   class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-receipt me-1"></i> Historique des paiements
                </a>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>
