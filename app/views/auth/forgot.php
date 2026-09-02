<!-- app/views/auth/forgot.php -->
<div class="auth-card">
    <div class="auth-header">
        <i class="bi bi-key-fill"></i>
        <h4>Mot de passe oublié</h4>
        <p>Entrez votre email pour recevoir un lien de réinitialisation</p>
    </div>

    <div class="auth-body">

        <?php if ($sent): ?>
        <div class="alert alert-success">
            <i class="bi bi-check-circle me-1"></i>
            Lien de réinitialisation généré.<br>
            <strong>Mode développement — lien direct :</strong><br>
            <a href="<?= htmlspecialchars($reset_link ?? '#') ?>" class="small">
                <?= htmlspecialchars($reset_link ?? '') ?>
            </a>
        </div>
        <a href="<?= Router::url('auth/login') ?>" class="btn-auth d-block text-center mt-3"
           style="text-decoration:none; padding:11px;">
            Retour à la connexion
        </a>

        <?php else: ?>

        <?php if ($error): ?>
        <div class="alert alert-danger py-2 small">
            <i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= Router::url('auth/doForgot') ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <div class="mb-4">
                <label class="form-label">Adresse email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control"
                           placeholder="votre@email.tg" required autofocus>
                </div>
            </div>

            <button type="submit" class="btn-auth">
                <i class="bi bi-send me-1"></i> Envoyer le lien
            </button>
        </form>

        <?php endif; ?>
    </div>

    <div class="auth-footer">
        <a href="<?= Router::url('auth/login') ?>"><i class="bi bi-arrow-left"></i> Retour à la connexion</a>
    </div>
</div>
