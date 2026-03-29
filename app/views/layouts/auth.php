<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'SGE') ?> — SGE</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendor/bootstrap-icons/css/bootstrap-icons.min.css">
    <style>
        :root {
            --primary: #0f3460;
            --accent:  #e94560;
        }
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .auth-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
            width: 100%;
            max-width: 420px;
            overflow: hidden;
        }
        .auth-header {
            background: linear-gradient(135deg, var(--primary), #1a5276);
            padding: 32px 24px 24px;
            text-align: center;
            color: white;
        }
        .auth-header i { font-size: 3rem; color: var(--accent); }
        .auth-header h4 { margin: 10px 0 4px; font-weight: 700; }
        .auth-header p  { font-size: 0.85rem; opacity: 0.8; margin: 0; }
        .auth-body { padding: 28px 32px; }
        .form-label { font-weight: 600; font-size: 0.85rem; color: #444; }
        .form-control {
            border-radius: 8px;
            border: 1.5px solid #ddd;
            padding: 10px 14px;
            font-size: 0.95rem;
        }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(15,52,96,0.12);
        }
        .btn-auth {
            background: linear-gradient(135deg, var(--primary), #1a5276);
            border: none;
            border-radius: 8px;
            padding: 11px;
            font-weight: 600;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
            width: 100%;
            color: white;
            transition: opacity 0.2s;
        }
        .btn-auth:hover { opacity: 0.9; color: white; }
        .auth-footer {
            text-align: center;
            padding: 16px 24px;
            background: #f8f9fa;
            border-top: 1px solid #eee;
            font-size: 0.85rem;
            color: #666;
        }
        .auth-footer a { color: var(--primary); text-decoration: none; font-weight: 600; }
        .input-group-text {
            background: #f8f9fa;
            border-right: none;
            border-radius: 8px 0 0 8px;
        }
        .input-group .form-control { border-left: none; border-radius: 0 8px 8px 0; }
        .toggle-pw { cursor: pointer; background: #f8f9fa; border-left: none; border-radius: 0 8px 8px 0; }
    </style>
</head>
<body>
    <?= $content ?>
    <script src="<?= BASE_URL ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
    // Toggle visibilité mot de passe
    document.querySelectorAll('.toggle-pw').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.closest('.input-group').querySelector('input');
            const icon  = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });
    });
    </script>
</body>
</html>
