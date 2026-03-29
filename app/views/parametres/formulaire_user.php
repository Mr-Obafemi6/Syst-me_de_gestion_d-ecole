<!-- app/views/parametres/formulaire_user.php -->
<?php $edit = !empty($userEdit['id']); ?>

<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-person-<?= $edit ? 'gear' : 'plus-fill' ?> text-primary fs-5"></i>
        <?= $edit ? 'Modifier l\'utilisateur' : 'Ajouter un utilisateur' ?>
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

        <form method="POST" action="<?= $edit
            ? Router::url('parametres/updateUser/' . $userEdit['id'])
            : Router::url('parametres/storeUser') ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nom <span class="text-danger">*</span></label>
                    <input type="text" name="nom" class="form-control <?= isset($errors['nom']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($userEdit['nom'] ?? '') ?>"
                           placeholder="Ex: KOFFI" required>
                    <?php if (isset($errors['nom'])): ?>
                    <div class="invalid-feedback"><?= $errors['nom'] ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Prénom <span class="text-danger">*</span></label>
                    <input type="text" name="prenom" class="form-control <?= isset($errors['prenom']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($userEdit['prenom'] ?? '') ?>"
                           placeholder="Ex: Koami" required>
                    <?php if (isset($errors['prenom'])): ?>
                    <div class="invalid-feedback"><?= $errors['prenom'] ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-12">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($userEdit['email'] ?? '') ?>"
                           placeholder="prenom.nom@ecole.tg" required>
                    <?php if (isset($errors['email'])): ?>
                    <div class="invalid-feedback"><?= $errors['email'] ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Rôle <span class="text-danger">*</span></label>
                    <select name="role" class="form-select" required>
                        <?php
                        $roles = ['admin' => '🛡️ Administrateur', 'professeur' => '👨‍🏫 Professeur', 'parent' => '👨‍👧 Parent'];
                        foreach ($roles as $val => $label):
                        ?>
                        <option value="<?= $val ?>"
                            <?= ($userEdit['role'] ?? 'professeur') === $val ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Statut</label>
                    <select name="actif" class="form-select">
                        <option value="1" <?= ($userEdit['actif'] ?? 1) ? 'selected' : '' ?>>✅ Actif</option>
                        <option value="0" <?= isset($userEdit['actif']) && !$userEdit['actif'] ? 'selected' : '' ?>>❌ Inactif</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">
                        Mot de passe <?= $edit ? '<span class="text-muted small">(laisser vide pour ne pas changer)</span>' : '<span class="text-danger">*</span>' ?>
                    </label>
                    <div class="input-group">
                        <input type="password" name="password" id="pw-input"
                               class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                               placeholder="<?= $edit ? 'Laisser vide pour ne pas modifier' : 'Minimum 8 caractères' ?>"
                               <?= $edit ? '' : 'required' ?>>
                        <button type="button" class="btn btn-outline-secondary toggle-pw">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <?php if (isset($errors['password'])): ?>
                    <div class="text-danger small mt-1"><?= $errors['password'] ?></div>
                    <?php endif; ?>
                    <?php if (!$edit): ?>
                    <div class="form-text">8 caractères min., 1 majuscule, 1 chiffre recommandés.</div>
                    <?php endif; ?>
                </div>
            </div>

            <hr class="my-4">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    <?= $edit ? 'Enregistrer les modifications' : 'Créer l\'utilisateur' ?>
                </button>
                <a href="<?= Router::url('parametres#tab-users') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-x me-1"></i> Annuler
                </a>
            </div>
        </form>
    </div>
</div>
</div>
</div>

<script>
document.querySelector('.toggle-pw')?.addEventListener('click', function() {
    const input = document.getElementById('pw-input');
    const icon  = this.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
});
</script>
