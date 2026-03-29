<!-- app/views/classes/formulaire.php -->
<?php $edit = !empty($classe['id']); ?>

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
            : Router::url('classes/store') ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <div class="mb-3">
                <label class="form-label">Nom de la classe <span class="text-danger">*</span></label>
                <input type="text" name="nom" class="form-control <?= isset($errors['nom']) ? 'is-invalid' : '' ?>"
                       value="<?= htmlspecialchars($classe['nom'] ?? '') ?>"
                       placeholder="Ex: 6ème A, Terminale C" required>
                <?php if (isset($errors['nom'])): ?>
                <div class="invalid-feedback"><?= $errors['nom'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Niveau <span class="text-danger">*</span></label>
                <select name="niveau" class="form-select <?= isset($errors['niveau']) ? 'is-invalid' : '' ?>" required>
                    <option value="">-- Choisir un niveau --</option>
                    <?php
                    $niveaux = ['CP1','CP2','CE1','CE2','CM1','CM2',
                                'Sixième','Cinquième','Quatrième','Troisième',
                                'Seconde','Première','Terminale'];
                    foreach ($niveaux as $n):
                    ?>
                    <option value="<?= $n ?>" <?= ($classe['niveau'] ?? '') === $n ? 'selected' : '' ?>>
                        <?= $n ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['niveau'])): ?>
                <div class="invalid-feedback"><?= $errors['niveau'] ?></div>
                <?php endif; ?>
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
