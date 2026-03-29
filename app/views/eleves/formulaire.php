<!-- app/views/eleves/formulaire.php -->
<?php $edit = !empty($eleve['id']); ?>

<div class="row justify-content-center">
<div class="col-lg-7">

<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-person-<?= $edit ? 'gear' : 'plus-fill' ?> text-primary fs-5"></i>
        <?= $edit ? 'Modifier l\'élève' : 'Ajouter un élève' ?>
    </div>
    <div class="card-body">

        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle me-1"></i>
            <strong>Corrigez les erreurs suivantes :</strong>
            <ul class="mb-0 mt-1">
                <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="POST"
              action="<?= $edit
                ? Router::url('eleves/update/' . $eleve['id'])
                : Router::url('eleves/store') ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <div class="row g-3">

                <!-- Nom -->
                <div class="col-md-6">
                    <label class="form-label">Nom <span class="text-danger">*</span></label>
                    <input type="text" name="nom" class="form-control <?= isset($errors['nom']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($eleve['nom'] ?? '') ?>"
                           placeholder="Ex: KOFFI" required>
                    <?php if (isset($errors['nom'])): ?>
                    <div class="invalid-feedback"><?= $errors['nom'] ?></div>
                    <?php endif; ?>
                </div>

                <!-- Prénom -->
                <div class="col-md-6">
                    <label class="form-label">Prénom <span class="text-danger">*</span></label>
                    <input type="text" name="prenom" class="form-control <?= isset($errors['prenom']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($eleve['prenom'] ?? '') ?>"
                           placeholder="Ex: Koami" required>
                    <?php if (isset($errors['prenom'])): ?>
                    <div class="invalid-feedback"><?= $errors['prenom'] ?></div>
                    <?php endif; ?>
                </div>

                <!-- Date de naissance -->
                <div class="col-md-6">
                    <label class="form-label">Date de naissance <span class="text-danger">*</span></label>
                    <input type="date" name="date_naissance"
                           class="form-control <?= isset($errors['date_naissance']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($eleve['date_naissance'] ?? '') ?>"
                           max="<?= date('Y-m-d', strtotime('-3 years')) ?>" required>
                    <?php if (isset($errors['date_naissance'])): ?>
                    <div class="invalid-feedback"><?= $errors['date_naissance'] ?></div>
                    <?php endif; ?>
                </div>

                <!-- Sexe -->
                <div class="col-md-6">
                    <label class="form-label">Sexe <span class="text-danger">*</span></label>
                    <select name="sexe" class="form-select <?= isset($errors['sexe']) ? 'is-invalid' : '' ?>" required>
                        <option value="">-- Choisir --</option>
                        <option value="M" <?= ($eleve['sexe'] ?? '') === 'M' ? 'selected' : '' ?>>♂ Masculin</option>
                        <option value="F" <?= ($eleve['sexe'] ?? '') === 'F' ? 'selected' : '' ?>>♀ Féminin</option>
                    </select>
                    <?php if (isset($errors['sexe'])): ?>
                    <div class="invalid-feedback"><?= $errors['sexe'] ?></div>
                    <?php endif; ?>
                </div>

                <!-- Classe -->
                <div class="col-md-6">
                    <label class="form-label">Classe <span class="text-danger">*</span></label>
                    <select name="classe_id" class="form-select <?= isset($errors['classe_id']) ? 'is-invalid' : '' ?>" required>
                        <option value="">-- Choisir une classe --</option>
                        <?php foreach ($classes as $cl): ?>
                        <option value="<?= $cl['id'] ?>"
                            <?= ($eleve['classe_id'] ?? 0) == $cl['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cl['nom'] . ' — ' . $cl['niveau']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['classe_id'])): ?>
                    <div class="invalid-feedback"><?= $errors['classe_id'] ?></div>
                    <?php endif; ?>
                </div>

                <!-- Matricule (lecture seule si modification) -->
                <?php if ($edit): ?>
                <div class="col-md-6">
                    <label class="form-label">Matricule</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($eleve['matricule'] ?? '') ?>"
                           readonly>
                    <div class="form-text">Généré automatiquement, non modifiable.</div>
                </div>
                <?php endif; ?>

            </div><!-- /row -->

            <hr class="my-4">

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    <?= $edit ? 'Enregistrer les modifications' : 'Ajouter l\'élève' ?>
                </button>
                <a href="<?= Router::url($edit ? 'eleves/fiche/' . $eleve['id'] : 'eleves') ?>"
                   class="btn btn-outline-secondary">
                    <i class="bi bi-x me-1"></i> Annuler
                </a>
            </div>

        </form>
    </div>
</div>

</div>
</div>
