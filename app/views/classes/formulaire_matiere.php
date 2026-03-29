<!-- app/views/classes/formulaire_matiere.php -->

<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-book-fill text-primary fs-5"></i>
        Ajouter une matière — <strong><?= htmlspecialchars($classe['nom']) ?></strong>
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
            <input type="hidden" name="classe_id"  value="<?= $classe['id'] ?>">

            <div class="mb-3">
                <label class="form-label">Nom de la matière <span class="text-danger">*</span></label>
                <input type="text" name="nom" class="form-control <?= isset($errors['nom']) ? 'is-invalid' : '' ?>"
                       value="<?= htmlspecialchars($matiere['nom'] ?? '') ?>"
                       placeholder="Ex: Mathématiques, Français, SVT…" required autofocus>
                <?php if (isset($errors['nom'])): ?>
                <div class="invalid-feedback"><?= $errors['nom'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Coefficient <span class="text-danger">*</span></label>
                <input type="number" name="coefficient"
                       class="form-control <?= isset($errors['coefficient']) ? 'is-invalid' : '' ?>"
                       value="<?= htmlspecialchars($matiere['coefficient'] ?? '1') ?>"
                       min="0.5" max="20" step="0.5" required>
                <div class="form-text">Entre 0.5 et 20. Utilisé pour le calcul de la moyenne pondérée.</div>
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
                        — <?= htmlspecialchars($prof['email']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php if (empty($profs)): ?>
                <div class="form-text text-warning">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    Aucun professeur créé. Créez d'abord un compte professeur dans les Paramètres.
                </div>
                <?php endif; ?>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-plus-circle me-1"></i> Ajouter la matière
                </button>
                <a href="<?= Router::url('classes/detail/' . $classe['id']) ?>"
                   class="btn btn-outline-secondary">
                    <i class="bi bi-x me-1"></i> Annuler
                </a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
