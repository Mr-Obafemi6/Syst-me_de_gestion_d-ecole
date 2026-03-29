<!-- app/views/auth/login.php -->
<div class="auth-card">
    <div class="auth-header">
        <i class="bi bi-mortarboard-fill"></i>
        <h4>SGE</h4>
        <p>Système de Gestion d'École</p>
    </div>

    <div class="auth-body">
        <h5 class="mb-4 text-center" style="color:#0f3460; font-weight:700;">Connexion</h5>

        <?php if ($expired): ?>
        <div class="alert alert-warning py-2 small">
            <i class="bi bi-clock"></i> Votre session a expiré. Veuillez vous reconnecter.
        </div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="alert alert-danger py-2 small">
            <i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= Router::url('auth/doLogin') ?>" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <div class="mb-3">
                <label class="form-label">Adresse email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control"
                            placeholder="admin@sge.tg"
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                            required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label d-flex justify-content-between">
                    Mot de passe
                    <a href="<?= Router::url('auth/forgot') ?>" class="text-muted" style="font-weight:400;font-size:0.82rem;">
                        Oublié ?
                    </a>
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" class="form-control"
                            placeholder="••••••••" required>
                    <button type="button" class="btn btn-outline-secondary toggle-pw">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-auth">
                <i class="bi bi-box-arrow-in-right me-1"></i> Se connecter
            </button>
        </form>
    </div>

    <div class="auth-footer">
        SGE v<?= APP_VERSION ?> &mdash; Université de Lomé
    </div>
</div>
