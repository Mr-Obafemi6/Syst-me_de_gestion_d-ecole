<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'SGE') ?> — SGE</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendor/bootstrap-icons/css/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #3b82f6;
            --primary-light: #60a5fa;
            --accent: #bfdbfe;
            --text: #0f172a;
            --muted: #64748b;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            font-family: 'Inter', 'Segoe UI', system-ui, Arial, sans-serif;
            color: var(--text);
            background-color: #e0f2fe !important;
            background-color: #e0f2fe !important;
            position: relative;
            overflow: hidden;
        }

        .watermark {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
            opacity: 0.15;
        }

        .watermark img {
            position: absolute;
            width: 90px;
            height: 90px;
            object-fit: contain;
        }

        body::before,
        body::after {
            content: '';
            position: absolute;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
            filter: blur(6px);
            pointer-events: none;
        }

        body::before { top: -80px; left: -70px; }
        body::after { right: -90px; bottom: -90px; }

        /* Loader page auth */
        #auth-loader {
            position: fixed;
            inset: 0;
            background: rgba(4, 15, 18, 0.65);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity .3s ease, visibility .3s ease;
        }

        #auth-loader.hidden { opacity: 0; visibility: hidden; }

        .auth-ring {
            width: 42px;
            height: 42px;
            border: 3px solid rgba(255,255,255,0.25);
            border-top-color: var(--accent);
            border-radius: 50%;
            animation: spin .8s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .auth-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 24px 70px rgba(0, 0, 0, 0.28);
            width: min(100%, 460px);
            overflow: hidden;
            animation: slideUp .4s ease;
            border: 1px solid rgba(255,255,255,0.16);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .auth-header {
            background: white;
            padding: 34px 28px 26px;
            text-align: center;
            color: var(--text);
            position: relative;
            overflow: hidden;
        }

        .auth-badge {
            width: 56px;
            height: 56px;
            margin: 0 auto 12px;
            display: grid;
            place-items: center;
            border-radius: 12px;
            background: var(--primary);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .auth-header i {
            font-size: 1.75rem;
            color: white;
        }

        .auth-header h4 { margin: 0 0 6px; font-weight: 700; font-size: 1.2rem; color: var(--text); }
        .auth-header p { font-size: 0.9rem; color: var(--muted); margin: 0; }

        .auth-body { padding: 28px 30px 24px; }

        .auth-intro { margin-bottom: 18px; }
        .auth-intro h5 {
            margin: 0 0 6px;
            color: #0f172a;
            font-weight: 700;
        }
        .auth-intro p {
            margin: 0;
            color: var(--muted);
            font-size: 0.92rem;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.875rem;
            color: #334155;
            margin-bottom: 7px;
        }

        .form-control {
            border-radius: 10px;
            border: 1.5px solid #dbe4ef;
            padding: 10px 14px;
            font-size: 0.95rem;
            transition: border-color .2s, box-shadow .2s;
        }

        .form-control:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 4px rgba(26, 122, 110, 0.12);
            outline: none;
        }

        .btn-auth {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 14px;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 0.2px;
            width: 100%;
            color: white;
            transition: transform .15s ease, box-shadow .2s ease;
            position: relative;
            box-shadow: 0 10px 22px rgba(26, 122, 110, 0.22);
        }

        .btn-auth:hover { color: white; transform: translateY(-1px); }
        .btn-auth:active { transform: scale(0.99); }
        .btn-auth.loading { opacity: .85; pointer-events: none; }
        .btn-auth.loading::after {
            content: '';
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255,255,255,.35);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .7s linear infinite;
        }

        .auth-footer {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            padding: 16px 24px 20px;
            background: #f8fafc;
            border-top: 1px solid #e8edf4;
            font-size: 0.82rem;
            color: #64748b;
        }

        .auth-footer a { color: var(--primary-light); text-decoration: none; font-weight: 600; }

        .input-group-text {
            background: #f8fafc;
            border-right: none;
            border-radius: 10px 0 0 10px;
            color: #64748b;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }

        .toggle-pw {
            cursor: pointer;
            background: #f8fafc;
            border-left: none;
            border-radius: 0 10px 10px 0;
        }

        @media (max-width: 576px) {
            body { padding: 16px; }
            .auth-body { padding: 24px 22px 20px; }
            .auth-header { padding: 28px 22px 22px; }
        }
    </style>
</head>
<body>
    <div class="watermark">
        <img src="<?= BASE_URL ?>/assets/img/embleme1.png" style="top: 5%; left: 3%;">
        <img src="<?= BASE_URL ?>/assets/img/embleme2.png" style="top: 15%; left: 12%;">
        <img src="<?= BASE_URL ?>/assets/img/ConnexionSystmedeGestionScolaire (1).png" style="top: 8%; left: 25%;">
        <img src="<?= BASE_URL ?>/assets/img/embleme1.png" style="top: 20%; left: 35%;">
        <img src="<?= BASE_URL ?>/assets/img/embleme2.png" style="top: 12%; left: 45%;">
        <img src="<?= BASE_URL ?>/assets/img/embleme1.png" style="top: 25%; left: 55%;">
        <img src="<?= BASE_URL ?>/assets/img/embleme2.png" style="top: 18%; left: 65%;">
        <img src="<?= BASE_URL ?>/assets/img/ConnexionSystmedeGestionScolaire (1).png" style="top: 10%; left: 75%;">
        <img src="<?= BASE_URL ?>/assets/img/embleme1.png" style="top: 22%; left: 85%;">
        <img src="<?= BASE_URL ?>/assets/img/embleme2.png" style="top: 15%; left: 92%;">
        <img src="<?= BASE_URL ?>/assets/img/embleme1.png" style="top: 45%; left: 6%;">
        <img src="<?= BASE_URL ?>/assets/img/embleme2.png" style="top: 55%; left: 16%;">
        <img src="<?= BASE_URL ?>/assets/img/ConnexionSystmedeGestionScolaire (1).png" style="top: 50%; left: 28%;">
        <img src="<?= BASE_URL ?>/assets/img/embleme1.png" style="top: 60%; left: 38%;">
        <img src="<?= BASE_URL ?>/assets/img/embleme2.png" style="top: 65%; left: 48%;">
    </div>
    <?php $content = $content ?? ''; ?>
    <?= $content ?>
    <script src="<?= BASE_URL ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
    // Loader auth
    const authLoader = document.createElement('div');
    authLoader.id = 'auth-loader';
    authLoader.innerHTML = '<div class="auth-ring"></div>';
    document.body.prepend(authLoader);
    window.addEventListener('load', () => {
        setTimeout(() => authLoader.classList.add('hidden'), 150);
    });

    // Toggle mot de passe
    document.querySelectorAll('.toggle-pw').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.closest('.input-group').querySelector('input');
            const icon  = btn.querySelector('i');
            if (!input) return;
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });
    });

    // Spinner bouton login
    document.querySelector('form')?.addEventListener('submit', function() {
        const btn = this.querySelector('.btn-auth');
        if (btn) {
            btn.classList.add('loading');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Connexion…';
        }
    });
    </script>
</body>
</html>
