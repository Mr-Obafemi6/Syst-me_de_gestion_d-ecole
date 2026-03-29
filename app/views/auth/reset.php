<!-- app/views/auth/reset.php -->
<div class="auth-card">
    <div class="auth-header">
        <i class="bi bi-shield-lock-fill"></i>
        <h4>Nouveau mot de passe</h4>
        <p>Choisissez un mot de passe sécurisé</p>
    </div>

    <div class="auth-body">

        <?php if ($error): ?>
        <div class="alert alert-danger py-2 small">
            <i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= Router::url('auth/doReset') ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <input type="hidden" name="token"      value="<?= htmlspecialchars($token) ?>">

            <div class="mb-3">
                <label class="form-label">Nouveau mot de passe</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="pw1" class="form-control"
                           placeholder="••••••••" required autofocus>
                    <button type="button" class="btn btn-outline-secondary toggle-pw">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <div class="form-text">8 caractères min., 1 majuscule, 1 chiffre.</div>
            </div>

            <div class="mb-4">
                <label class="form-label">Confirmer le mot de passe</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="confirm" id="pw2" class="form-control"
                           placeholder="••••••••" required>
                    <button type="button" class="btn btn-outline-secondary toggle-pw">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Indicateur de force -->
            <div class="mb-3">
                <div class="d-flex gap-1 mb-1" id="strength-bars">
                    <div class="flex-fill rounded" style="height:4px;background:#ddd" id="sb1"></div>
                    <div class="flex-fill rounded" style="height:4px;background:#ddd" id="sb2"></div>
                    <div class="flex-fill rounded" style="height:4px;background:#ddd" id="sb3"></div>
                    <div class="flex-fill rounded" style="height:4px;background:#ddd" id="sb4"></div>
                </div>
                <small id="strength-label" class="text-muted"></small>
            </div>

            <button type="submit" class="btn-auth">
                <i class="bi bi-check-circle me-1"></i> Enregistrer le mot de passe
            </button>
        </form>
    </div>
</div>

<script>
document.getElementById('pw1').addEventListener('input', function() {
    const pw = this.value;
    let score = 0;
    if (pw.length >= 8)            score++;
    if (/[A-Z]/.test(pw))          score++;
    if (/[0-9]/.test(pw))          score++;
    if (/[^A-Za-z0-9]/.test(pw))   score++;

    const colors  = ['#e74c3c','#e67e22','#f1c40f','#27ae60'];
    const labels  = ['Faible','Moyen','Bien','Fort'];
    const bars    = ['sb1','sb2','sb3','sb4'];

    bars.forEach((id, i) => {
        document.getElementById(id).style.background = i < score ? colors[score-1] : '#ddd';
    });
    document.getElementById('strength-label').textContent =
        pw.length ? labels[score-1] || '' : '';
});
</script>
