<!-- app/views/errors/403.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>403 — Accès refusé</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendor/bootstrap/css/bootstrap.min.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height:100vh">
<div class="text-center">
    <h1 class="display-1 fw-bold text-danger">403</h1>
    <p class="lead">Vous n'avez pas les droits pour accéder à cette page.</p>
    <a href="<?= BASE_URL ?>/dashboard" class="btn btn-primary">Retour au tableau de bord</a>
</div>
</body>
</html>
