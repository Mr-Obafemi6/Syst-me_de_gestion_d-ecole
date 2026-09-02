<!-- app/views/classes/formulaire_matiere.php -->

<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-book-fill text-primary fs-5"></i>
        Assigner une matière — <strong><?= htmlspecialchars($classe['libelle_complete'] ?? $classe['nom']) ?></strong>
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

        <form method="POST" action="<?= Router::url('classes/storeMatiere') ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <input type="hidden" name="classe_id" value="<?= $classe['id'] ?>">

            <?php if (!empty($catalogue)): ?>
            <div class="mb-3">
                <label class="form-label">Matière du catalogue</label>
                <select name="matiere_id" class="form-select" id="matiere_catalogue">
                    <option value="">— Créer une nouvelle matière —</option>
                    <?php foreach ($catalogue as $m): ?>
                    <option value="<?= (int) $m['id'] ?>"
                        <?= ((int) ($matiere['matiere_id'] ?? 0)) === (int) $m['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($m['nom']) ?> (<?= htmlspecialchars($m['code'] ?? '—') ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <div class="mb-3" id="bloc-nouvelle-matiere">
                <label class="form-label">Ou nouveau nom <span class="text-danger">*</span></label>
                <input type="text" name="nom" class="form-control <?= isset($errors['nom']) ? 'is-invalid' : '' ?>"
                       value="<?= htmlspecialchars($matiere['nom'] ?? '') ?>"
                       placeholder="Ex: Mathématiques, Physique-Chimie…">
                <?php if (isset($errors['nom'])): ?>
                <div class="invalid-feedback"><?= $errors['nom'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Coefficient <span class="text-danger">*</span></label>
                <input type="number" name="coefficient"
                       class="form-control <?= isset($errors['coefficient']) ? 'is-invalid' : '' ?>"
                       value="<?= htmlspecialchars((string) ($matiere['coefficient'] ?? '1')) ?>"
                       min="0.5" max="20" step="0.5" required>
                <div class="form-text">Entre 0,5 et 20 pour le calcul de la moyenne pondérée.</div>
                <?php if (isset($errors['coefficient'])): ?>
                <div class="invalid-feedback"><?= $errors['coefficient'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label class="form-label">Professeur assigné</label>
                <select name="prof_id" class="form-select">
                    <option value="">-- Aucun (à assigner plus tard) --</option>
                    <?php foreach ($profs as $prof): ?>
                    <option value="<?= $prof['id'] ?>"
                        <?= ($matiere['prof_id'] ?? 0) == $prof['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($prof['prenom'] . ' ' . $prof['nom']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-plus-circle me-1"></i> Assigner à la classe
                </button>
                <a href="<?= Router::url('classes/detail/' . $classe['id']) ?>" class="btn btn-outline-secondary">
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
    const sel = document.getElementById('matiere_catalogue');
    const bloc = document.getElementById('bloc-nouvelle-matiere');
    if (!sel || !bloc) return;
    const toggle = () => { bloc.style.display = sel.value ? 'none' : 'block'; };
    sel.addEventListener('change', toggle);
    toggle();
})();
</script>
