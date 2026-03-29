<!-- app/views/auth/profil.php -->
<div class="row justify-content-center">
<div class="col-lg-6">

    <?php if ($error): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($flash)): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
        <?= htmlspecialchars($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Carte infos -->
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="bi bi-person-circle text-primary fs-5"></i>
            Informations du compte
        </div>
        <div class="card-body">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                     style="width:56px;height:56px;font-size:1.5rem;font-weight:700;">
                    <?= strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1)) ?>
                </div>
                <div>
                    <div class="fw-bold fs-5"><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></div>
                    <span class="badge bg-primary"><?= htmlspecialchars(ucfirst($user['role'])) ?></span>
                </div>
            </div>
            <table class="table table-sm">
                <tr><th style="width:40%">Email</th><td><?= htmlspecialchars($user['email']) ?></td></tr>
                <tr><th>Rôle</th><td><?= htmlspecialchars(ucfirst($user['role'])) ?></td></tr>
            </table>
        </div>
    </div>

    <!-- Changer mot de passe -->
    <div class="card">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="bi bi-shield-lock text-primary fs-5"></i>
            Changer le mot de passe
        </div>
        <div class="card-body">
            <form method="POST" action="<?= Router::url('auth/updatePassword') ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                <div class="mb-3">
                    <label class="form-label">Mot de passe actuel</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nouveau mot de passe</label>
                    <input type="password" name="new_password" class="form-control"
                           placeholder="8 car. min., 1 maj., 1 chiffre" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Confirmer</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i> Mettre à jour
                </button>
            </form>
        </div>
    </div>

</div>
</div>
