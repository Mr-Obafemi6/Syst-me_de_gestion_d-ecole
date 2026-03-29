<!-- app/views/parametres/index.php -->

<!-- Onglets -->
<ul class="nav nav-tabs mb-4" id="paramTabs">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#tab-ecole">
            <i class="bi bi-building me-1"></i> École
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tab-annees">
            <i class="bi bi-calendar3 me-1"></i> Années scolaires
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tab-users">
            <i class="bi bi-people me-1"></i> Utilisateurs
        </a>
    </li>
</ul>

<div class="tab-content">

    <!-- ── ONGLET ÉCOLE ── -->
    <div class="tab-pane fade show active" id="tab-ecole">
        <div class="row justify-content-center">
        <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-building me-1 text-primary"></i> Informations de l'établissement
            </div>
            <div class="card-body">
                <form method="POST" action="<?= Router::url('parametres/saveEcole') ?>">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                    <div class="mb-3">
                        <label class="form-label">Nom de l'école</label>
                        <input type="text" name="nom_ecole" class="form-control"
                               value="<?= htmlspecialchars($params['nom_ecole'] ?? '') ?>"
                               placeholder="Ex: Groupe Scolaire de Lomé">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Adresse</label>
                        <input type="text" name="adresse" class="form-control"
                               value="<?= htmlspecialchars($params['adresse'] ?? '') ?>"
                               placeholder="Ex: Lomé, Togo">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Téléphone</label>
                            <input type="text" name="telephone" class="form-control"
                                   value="<?= htmlspecialchars($params['telephone'] ?? '') ?>"
                                   placeholder="+228 00 00 00 00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control"
                                   value="<?= htmlspecialchars($params['email'] ?? '') ?>"
                                   placeholder="contact@ecole.tg">
                        </div>
                    </div>

                    <hr class="my-3">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="bi bi-cash me-1"></i> Frais de scolarité (FCFA)
                    </h6>
                    <div class="row g-3">
                        <?php
                        $fraisChamps = [
                            'frais_scol_primaire' => 'Primaire',
                            'frais_scol_college'  => 'Collège',
                            'frais_scol_lycee'    => 'Lycée',
                        ];
                        foreach ($fraisChamps as $cle => $label):
                        ?>
                        <div class="col-md-4">
                            <label class="form-label"><?= $label ?></label>
                            <div class="input-group">
                                <input type="number" name="<?= $cle ?>" class="form-control"
                                       value="<?= htmlspecialchars($params[$cle] ?? '') ?>"
                                       min="0" step="1000">
                                <span class="input-group-text">FCFA</span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </div>
        </div>
    </div>

    <!-- ── ONGLET ANNÉES SCOLAIRES ── -->
    <div class="tab-pane fade" id="tab-annees">
        <div class="row g-4">

            <!-- Liste des années -->
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-list-ul me-1 text-primary"></i> Années scolaires
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Année</th>
                                    <th>Début</th>
                                    <th>Fin</th>
                                    <th class="text-center">Statut</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($annees as $an): ?>
                            <tr>
                                <td class="fw-bold"><?= htmlspecialchars($an['libelle']) ?></td>
                                <td><?= date('d/m/Y', strtotime($an['date_debut'])) ?></td>
                                <td><?= date('d/m/Y', strtotime($an['date_fin'])) ?></td>
                                <td class="text-center">
                                    <?php if ($an['active']): ?>
                                    <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if (!$an['active']): ?>
                                    <form method="POST"
                                          action="<?= Router::url('parametres/activerAnnee/' . $an['id']) ?>"
                                          class="d-inline">
                                        <input type="hidden" name="csrf_token"
                                               value="<?= htmlspecialchars($csrf_token) ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-success"
                                                data-confirm="Activer l'année <?= htmlspecialchars($an['libelle']) ?> ?">
                                            <i class="bi bi-check-circle"></i> Activer
                                        </button>
                                    </form>
                                    <?php else: ?>
                                    <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Formulaire nouvelle année -->
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-plus-circle me-1 text-primary"></i> Nouvelle année scolaire
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?= Router::url('parametres/saveAnnee') ?>">
                            <input type="hidden" name="csrf_token"
                                   value="<?= htmlspecialchars($csrf_token) ?>">
                            <div class="mb-3">
                                <label class="form-label">Libellé</label>
                                <input type="text" name="libelle" class="form-control"
                                       placeholder="Ex: 2025-2026" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Date de début</label>
                                <input type="date" name="date_debut" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Date de fin</label>
                                <input type="date" name="date_fin" class="form-control" required>
                            </div>
                            <div class="mb-4 form-check">
                                <input type="checkbox" name="active" value="1"
                                       class="form-check-input" id="chkActive">
                                <label class="form-check-label" for="chkActive">
                                    Définir comme année active
                                </label>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-plus-circle me-1"></i> Créer l'année
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── ONGLET UTILISATEURS ── -->
    <div class="tab-pane fade" id="tab-users">
        <div class="d-flex justify-content-end mb-3">
            <a href="<?= Router::url('parametres/ajouterUser') ?>" class="btn btn-success">
                <i class="bi bi-person-plus-fill me-1"></i> Ajouter un utilisateur
            </a>
        </div>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-people me-1 text-primary"></i> Utilisateurs du système</span>
                <span class="badge bg-primary"><?= count($users) ?> compte<?= count($users) > 1 ? 's' : '' ?></span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Nom & Prénom</th>
                            <th>Email</th>
                            <th class="text-center">Rôle</th>
                            <th class="text-center">Statut</th>
                            <th>Depuis</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $u): ?>
                    <?php $isMe = $u['id'] == AuthMiddleware::user()['id']; ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle text-white d-flex align-items-center justify-content-center"
                                     style="width:32px;height:32px;font-size:.75rem;font-weight:700;flex-shrink:0;
                                            background:<?= $u['role']==='admin' ? '#0f3460' : ($u['role']==='professeur' ? '#1a7a4a' : '#6c757d') ?>">
                                    <?= strtoupper(substr($u['prenom'],0,1).substr($u['nom'],0,1)) ?>
                                </div>
                                <span class="fw-semibold">
                                    <?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?>
                                    <?php if ($isMe): ?>
                                    <span class="badge bg-warning text-dark ms-1">Vous</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </td>
                        <td class="small text-muted"><?= htmlspecialchars($u['email']) ?></td>
                        <td class="text-center">
                            <?php
                            $roleCls  = ['admin'=>'primary','professeur'=>'success','parent'=>'secondary'];
                            $roleIcon = ['admin'=>'shield-fill','professeur'=>'person-video3','parent'=>'person-heart'];
                            ?>
                            <span class="badge bg-<?= $roleCls[$u['role']] ?? 'secondary' ?>">
                                <i class="bi bi-<?= $roleIcon[$u['role']] ?? 'person' ?> me-1"></i>
                                <?= ucfirst($u['role']) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge <?= $u['actif'] ? 'bg-success' : 'bg-danger' ?>">
                                <?= $u['actif'] ? 'Actif' : 'Inactif' ?>
                            </span>
                        </td>
                        <td class="small text-muted">
                            <?= date('d/m/Y', strtotime($u['created_at'])) ?>
                        </td>
                        <td class="text-center">
                            <a href="<?= Router::url('parametres/modifierUser/' . $u['id']) ?>"
                               class="btn btn-sm btn-outline-warning" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php if (!$isMe): ?>
                            <form method="POST"
                                  action="<?= Router::url('parametres/toggleUser/' . $u['id']) ?>"
                                  class="d-inline">
                                <input type="hidden" name="csrf_token"
                                       value="<?= htmlspecialchars($csrf_token) ?>">
                                <button type="submit"
                                        class="btn btn-sm <?= $u['actif'] ? 'btn-outline-danger' : 'btn-outline-success' ?>"
                                        data-confirm="<?= $u['actif'] ? 'Désactiver' : 'Activer' ?> cet utilisateur ?">
                                    <i class="bi bi-<?= $u['actif'] ? 'person-x' : 'person-check' ?>"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div><!-- /tab-content -->

<script>
// Restaurer l'onglet actif depuis l'URL hash
const hash = window.location.hash;
if (hash) {
    const tab = document.querySelector(`[href="${hash}"]`);
    if (tab) new bootstrap.Tab(tab).show();
}
document.querySelectorAll('[data-bs-toggle="tab"]').forEach(t => {
    t.addEventListener('shown.bs.tab', e => {
        history.replaceState(null, '', e.target.getAttribute('href'));
    });
});
</script>
