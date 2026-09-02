<!-- app/views/auth/login.php -->
<?php
$expired = $expired ?? false;
$error = $error ?? null;
$csrf_token = $csrf_token ?? '';
?>
<div class="auth-card">
    <div class="auth-header">
        <div class="auth-badge">
            <i class="bi bi-mortarboard-fill"></i>
        </div>
        <h4>SYSTEME DE GESTION ECOLE</h4>
        <p>Votre espace de gestion scolaire, en toute simplicité</p>
    </div>

    <div class="auth-body">
        <div class="auth-intro">
            <h5>Connexion</h5>
            <p>Accédez à votre espace personnel</p>
        </div>

        <?php if ($expired): ?>
        <div class="alert alert-warning py-2 small rounded-3">
            <i class="bi bi-clock me-1"></i> Votre session a expiré. Veuillez vous reconnecter.
        </div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="alert alert-danger py-2 small rounded-3">
            <i class="bi bi-exclamation-circle me-1"></i> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= Router::url('auth/doLogin') ?>" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <div class="mb-3">
                <label class="form-label">Adresse e-mail</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control"
                            placeholder="exemple@ecole.fr"
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                            required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label d-flex justify-content-between align-items-center">
                    Mot de passe
                    <a href="<?= Router::url('auth/forgot') ?>" class="text-muted text-decoration-none small">
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
                Se connecter <i class="bi bi-arrow-right ms-1"></i>
            </button>
        </form>
    </div>

    <div class="auth-footer">
        <span>SGE v<?= APP_VERSION ?></span>
    </div>
</div>
