<!-- app/views/paiements/formulaire.php -->

<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-cash-coin text-success fs-5"></i>
        Enregistrer un paiement de scolarité
    </div>
    <div class="card-body">

        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= Router::url('paiements/store') ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <!-- Recherche élève -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Élève <span class="text-danger">*</span></label>
                <?php if ($eleve): ?>
                <!-- Élève déjà sélectionné -->
                <input type="hidden" name="eleve_id" value="<?= $eleve['id'] ?>">
                <div class="d-flex align-items-center gap-3 p-3 border rounded bg-light">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                         style="width:44px;height:44px;font-weight:700">
                        <?= strtoupper(substr($eleve['prenom'],0,1) . substr($eleve['nom'],0,1)) ?>
                    </div>
                    <div class="flex-fill">
                        <div class="fw-bold"><?= htmlspecialchars($eleve['prenom'] . ' ' . $eleve['nom']) ?></div>
                        <div class="text-muted small">
                            <?= htmlspecialchars($eleve['matricule']) ?> —
                            <?= htmlspecialchars($eleve['classe_nom'] ?? '') ?>
                        </div>
                        <?php if ($totalPaye > 0): ?>
                        <div class="text-success small">
                            <i class="bi bi-check-circle me-1"></i>
                            Déjà payé cette année :
                            <strong><?= number_format($totalPaye, 0, ',', ' ') ?> FCFA</strong>
                        </div>
                        <?php endif; ?>
                    </div>
                    <a href="<?= Router::url('paiements/ajouter') ?>" class="btn btn-sm btn-outline-secondary">
                        Changer
                    </a>
                </div>
                <?php else: ?>
                <!-- Recherche dynamique -->
                <div class="position-relative">
                    <input type="text" id="search-eleve" class="form-control"
                           placeholder="Tapez le nom ou matricule..."
                           autocomplete="off">
                    <div id="search-results" class="position-absolute w-100 bg-white border rounded shadow-sm"
                         style="z-index:1000;display:none;max-height:250px;overflow-y:auto"></div>
                </div>
                <input type="hidden" name="eleve_id" id="eleve-id-input" value="">
                <div id="eleve-selected" class="mt-2" style="display:none"></div>
                <?php if (isset($errors['eleve_id'])): ?>
                <div class="text-danger small mt-1"><?= $errors['eleve_id'] ?></div>
                <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Montant -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Montant (FCFA) <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="number" name="montant_fcfa"
                           class="form-control <?= isset($errors['montant_fcfa']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars((string)($data['montant_fcfa'] ?? '')) ?>"
                           min="100" step="100" placeholder="Ex: 50000" required>
                    <span class="input-group-text fw-bold">FCFA</span>
                </div>
                <!-- Raccourcis montants courants -->
                <div class="mt-2 d-flex gap-1 flex-wrap">
                    <?php foreach ([25000, 50000, 75000, 100000] as $m): ?>
                    <button type="button" class="btn btn-sm btn-outline-secondary raccourci-montant"
                            data-montant="<?= $m ?>">
                        <?= number_format($m, 0, ',', ' ') ?> FCFA
                    </button>
                    <?php endforeach; ?>
                </div>
                <?php if (isset($errors['montant_fcfa'])): ?>
                <div class="invalid-feedback d-block"><?= $errors['montant_fcfa'] ?></div>
                <?php endif; ?>
            </div>

            <div class="row g-3">
                <!-- Date -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Date de paiement <span class="text-danger">*</span></label>
                    <input type="date" name="date_paiement"
                           class="form-control <?= isset($errors['date_paiement']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($data['date_paiement'] ?? date('Y-m-d')) ?>"
                           max="<?= date('Y-m-d') ?>" required>
                </div>

                <!-- Mode de paiement -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Mode de paiement</label>
                    <select name="mode_paiement" class="form-select">
                        <?php
                        $modes = ['especes' => '💵 Espèces', 'mobile_money' => '📱 Mobile Money', 'virement' => '🏦 Virement'];
                        foreach ($modes as $val => $label):
                        ?>
                        <option value="<?= $val ?>"
                            <?= ($data['mode_paiement'] ?? 'especes') === $val ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Statut -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="paye"    <?= ($data['statut'] ?? 'paye') === 'paye'    ? 'selected' : '' ?>>✅ Payé intégralement</option>
                        <option value="partiel" <?= ($data['statut'] ?? '') === 'partiel' ? 'selected' : '' ?>>⏳ Paiement partiel</option>
                    </select>
                </div>

                <!-- Commentaire -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Commentaire (optionnel)</label>
                    <input type="text" name="commentaire" class="form-control"
                           value="<?= htmlspecialchars($data['commentaire'] ?? '') ?>"
                           placeholder="Ex: Tranche 1/3">
                </div>
            </div>

            <!-- Année scolaire -->
            <?php if ($annee): ?>
            <div class="alert alert-info py-2 mt-3 small">
                <i class="bi bi-calendar3 me-1"></i>
                Année scolaire active : <strong><?= htmlspecialchars($annee['libelle']) ?></strong>
            </div>
            <?php else: ?>
            <div class="alert alert-danger mt-3">
                <i class="bi bi-exclamation-triangle me-1"></i>
                Aucune année scolaire active. Configurez-en une dans les Paramètres.
            </div>
            <?php endif; ?>

            <hr class="my-4">

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success" <?= !$annee ? 'disabled' : '' ?>>
                    <i class="bi bi-check-circle me-1"></i> Enregistrer le paiement
                </button>
                <a href="<?= Router::url('paiements') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-x me-1"></i> Annuler
                </a>
            </div>
        </form>
    </div>
</div>
</div>
</div>

<script>
const BASE_URL = '<?= BASE_URL ?>';

// Raccourcis montants
document.querySelectorAll('.raccourci-montant').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelector('[name="montant_fcfa"]').value = btn.dataset.montant;
    });
});

// Recherche élève dynamique
const searchInput = document.getElementById('search-eleve');
const searchResults = document.getElementById('search-results');
const eleveIdInput = document.getElementById('eleve-id-input');
const eleveSelected = document.getElementById('eleve-selected');

if (searchInput) {
    let timer = null;
    searchInput.addEventListener('input', function() {
        clearTimeout(timer);
        const q = this.value.trim();
        if (q.length < 2) { searchResults.style.display = 'none'; return; }
        timer = setTimeout(() => rechercherEleves(q), 300);
    });

    document.addEventListener('click', e => {
        if (!searchInput.contains(e.target)) searchResults.style.display = 'none';
    });
}

async function rechercherEleves(q) {
    try {
        const resp = await fetch(`${BASE_URL}/eleves?q=${encodeURIComponent(q)}&ajax=1`);
        // Fallback : on simule via une recherche simple
        searchResults.innerHTML = `
            <div class="p-2 text-muted small">
                <i class="bi bi-info-circle me-1"></i>
                Utilisez le lien depuis la
                <a href="${BASE_URL}/eleves?q=${encodeURIComponent(q)}" target="_blank">fiche élève</a>
                pour pré-sélectionner l'élève.
            </div>`;
        searchResults.style.display = 'block';
    } catch(e) {}
}

function selectionnerEleve(id, nom, matricule, classe) {
    eleveIdInput.value = id;
    searchInput.style.display = 'none';
    searchResults.style.display = 'none';
    eleveSelected.style.display = 'block';
    eleveSelected.innerHTML = `
        <div class="d-flex align-items-center gap-2 p-2 border rounded bg-light">
            <i class="bi bi-person-check-fill text-success fs-4"></i>
            <div>
                <div class="fw-bold">${nom}</div>
                <div class="text-muted small">${matricule} — ${classe}</div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary ms-auto"
                    onclick="resetEleve()">Changer</button>
        </div>`;
}

function resetEleve() {
    eleveIdInput.value = '';
    searchInput.style.display = 'block';
    searchInput.value = '';
    eleveSelected.style.display = 'none';
}
</script>
