<!-- app/views/classes/formulaire.php -->
<?php
$classe = isset($classe) && is_array($classe) ? $classe : [];
$errors = isset($errors) && is_array($errors) ? $errors : [];
$csrf_token = isset($csrf_token) ? (string) $csrf_token : '';
$niveauxGroupes = isset($niveauxGroupes) && is_array($niveauxGroupes) ? $niveauxGroupes : [];
$profs = isset($profs) && is_array($profs) ? $profs : [];
$annees = isset($annees) && is_array($annees) ? $annees : [];
$edit = !empty($classe['id']);
?>

<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-building text-primary fs-5"></i>
        <?= $edit ? 'Modifier la classe' : 'Ajouter une classe' ?>
    </div>
    <div class="card-body">

        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle me-1"></i>
            <ul class="mb-0 mt-1">
                <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= $edit
            ? Router::url('classes/update/' . $classe['id'])
            : Router::url('classes/store') ?>" id="form-classe">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <div class="mb-3">
                <label class="form-label">Niveau / Série <span class="text-danger">*</span></label>
                <select name="niveau_id" id="niveau_id" class="form-select <?= isset($errors['niveau']) ? 'is-invalid' : '' ?>" required>
                    <option value="">-- Choisir un niveau --</option>
                    <?php foreach ($niveauxGroupes as $cycleKey => $groupe): ?>
                    <optgroup label="<?= htmlspecialchars($groupe['label']) ?>">
                        <?php foreach ($groupe['niveaux'] as $n): ?>
                        <option value="<?= (int) $n['id'] ?>" data-nom="<?= htmlspecialchars($n['nom']) ?>"
                            <?= ((int) ($classe['niveau_id'] ?? 0)) === (int) $n['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($n['nom']) ?>
                        </option>
                        <?php endforeach; ?>
                    </optgroup>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="niveau" id="niveau_nom" value="<?= htmlspecialchars($classe['niveau'] ?? '') ?>">
                <?php if (isset($errors['niveau'])): ?>
                <div class="invalid-feedback"><?= $errors['niveau'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Division <span class="text-danger">*</span></label>
                <input type="text" name="nom" class="form-control <?= isset($errors['nom']) ? 'is-invalid' : '' ?>"
                       value="<?= htmlspecialchars($classe['nom'] ?? '') ?>"
                       placeholder="Ex: A, B, C" required>
                <div class="form-text">
                    Collège : 6ème A, 5ème B… — Lycée : Terminale D A, 1ère A4 B…
                </div>
                <?php if (isset($errors['nom'])): ?>
                <div class="invalid-feedback"><?= $errors['nom'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Professeur principal</label>
                <select name="enseignant_id" class="form-select">
                    <option value="">-- Aucun enseignant --</option>
                    <?php foreach ($profs as $prof): ?>
                    <option value="<?= (int) $prof['id'] ?>" <?= ((int) ($classe['enseignant_id'] ?? 0)) === (int) $prof['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($prof['prenom'] . ' ' . $prof['nom']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Effectif maximum</label>
                    <input type="number" name="effectif_maximum" min="1" class="form-control" value="<?= htmlspecialchars((string) ($classe['effectif_maximum'] ?? 40)) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="1" <?= ((int) ($classe['statut'] ?? 1)) === 1 ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= ((int) ($classe['statut'] ?? 1)) === 0 ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Année scolaire <span class="text-danger">*</span></label>
                <select name="annee_scolaire_id" class="form-select <?= isset($errors['annee_scolaire_id']) ? 'is-invalid' : '' ?>" required>
                    <option value="">-- Choisir une année --</option>
                    <?php foreach ($annees as $a): ?>
                    <option value="<?= $a['id'] ?>"
                        <?= ($classe['annee_scolaire_id'] ?? 0) == $a['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($a['libelle']) ?>
                        <?= $a['active'] ? '(active)' : '' ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['annee_scolaire_id'])): ?>
                <div class="invalid-feedback"><?= $errors['annee_scolaire_id'] ?></div>
                <?php endif; ?>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    <?= $edit ? 'Enregistrer' : 'Créer la classe' ?>
                </button>
                <a href="<?= Router::url($edit ? 'classes/detail/' . $classe['id'] : 'classes') ?>"
                   class="btn btn-outline-secondary">
                    <i class="bi bi-x me-1"></i> Annuler
                </a>
            </div>
        </form>
    </div>
</div>
</div>
</div>

<script>
(function () {
    const select = document.getElementById('niveau_id');
    const hidden = document.getElementById('niveau_nom');
    if (!select || !hidden) return;
    const sync = () => {
        const opt = select.options[select.selectedIndex];
        hidden.value = opt && opt.dataset.nom ? opt.dataset.nom : '';
    };
    select.addEventListener('change', sync);
    sync();
})();
</script>
