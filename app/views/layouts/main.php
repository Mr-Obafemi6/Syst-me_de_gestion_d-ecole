<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'SGE') ?> — SGE</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendor/bootstrap/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendor/bootstrap-icons/css/bootstrap-icons.min.css">
    <!-- CSS principal -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css">
</head>
<body>

<div class="d-flex" id="wrapper">

    <!-- ===== SIDEBAR ===== -->
    <nav id="sidebar">
        <!-- Logo / Nom école -->
        <div class="sidebar-brand">
            <i class="bi bi-mortarboard-fill"></i>
            <span>SGE</span>
        </div>

        <!-- Navigation -->
        <ul class="sidebar-nav">
            <li class="nav-label">PRINCIPAL</li>

            <li class="<?= Router::is('dashboard') ? 'active' : '' ?>">
                <a href="<?= Router::url('dashboard') ?>">
                    <i class="bi bi-speedometer2"></i> Tableau de bord
                </a>
            </li>

            <?php if (AuthMiddleware::hasRole(ROLE_ADMIN) || AuthMiddleware::hasRole(ROLE_PROF)): ?>
            <li class="nav-label">SCOLARITÉ</li>

            <li class="<?= Router::is('eleves') ? 'active' : '' ?>">
                <a href="<?= Router::url('eleves') ?>">
                    <i class="bi bi-people-fill"></i> Élèves
                </a>
            </li>

            <li class="<?= Router::is('classes') ? 'active' : '' ?>">
                <a href="<?= Router::url('classes') ?>">
                    <i class="bi bi-building"></i> Classes
                </a>
            </li>

            <li class="<?= Router::is('notes') ? 'active' : '' ?>">
                <a href="<?= Router::url('notes') ?>">
                    <i class="bi bi-pencil-square"></i> Notes
                </a>
            </li>

            <li class="<?= Router::is('bulletins') ? 'active' : '' ?>">
                <a href="<?= Router::url('bulletins') ?>">
                    <i class="bi bi-file-earmark-text"></i> Bulletins
                </a>
            </li>

            <li class="<?= Router::is('absences') ? 'active' : '' ?>">
                <a href="<?= Router::url('absences') ?>">
                    <i class="bi bi-calendar-x"></i> Absences
                </a>
            </li>

            <li class="<?= Router::is('export') ? 'active' : '' ?>">
                <a href="<?= Router::url('export') ?>">
                    <i class="bi bi-download"></i> Export CSV
                </a>
            </li>
            <?php endif; ?>

            <?php if (AuthMiddleware::hasRole(ROLE_ADMIN)): ?>
            <li class="nav-label">FINANCES</li>

            <li class="<?= Router::is('paiements') ? 'active' : '' ?>">
                <a href="<?= Router::url('paiements') ?>">
                    <i class="bi bi-cash-coin"></i> Paiements
                </a>
            </li>

            <li class="nav-label">ADMINISTRATION</li>

            <li class="<?= Router::is('parametres') ? 'active' : '' ?>">
                <a href="<?= Router::url('parametres') ?>">
                    <i class="bi bi-gear-fill"></i> Paramètres
                </a>
            </li>
            <?php endif; ?>
        </ul>

        <!-- Infos utilisateur bas de sidebar -->
        <div class="sidebar-footer">
            <a href="<?= Router::url('auth/profil') ?>" class="sidebar-user text-decoration-none">
                <i class="bi bi-person-circle"></i>
                <div>
                    <div class="user-name"><?= htmlspecialchars(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?></div>
                    <div class="user-role"><?= htmlspecialchars(ucfirst($user['role'] ?? '')) ?></div>
                </div>
            </a>
        </div>
    </nav>
    <!-- ===== FIN SIDEBAR ===== -->

    <!-- ===== CONTENU PRINCIPAL ===== -->
    <div id="page-content">

        <!-- Topbar -->
        <header class="topbar">
            <button id="sidebarToggle" class="btn btn-sm">
                <i class="bi bi-list fs-4"></i>
            </button>
            <h5 class="mb-0 d-none d-md-block"><?= htmlspecialchars($pageTitle ?? '') ?></h5>

            <!-- Recherche globale -->
            <div class="flex-fill mx-3" style="max-width:380px">
                <div class="position-relative">
                    <input type="text" id="global-search" class="form-control form-control-sm"
                           placeholder="Rechercher élève, classe, reçu..."
                           autocomplete="off">
                    <div id="search-dropdown"
                         class="position-absolute w-100 bg-white border rounded shadow-sm"
                         style="z-index:1050;display:none;max-height:320px;overflow-y:auto;top:100%;left:0">
                    </div>
                </div>
            </div>

            <div class="topbar-right">
                <a href="<?= Router::url('auth/logout') ?>" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-box-arrow-right"></i> Déconnexion
                </a>
            </div>
        </header>

        <!-- Flash message -->
        <?php
        $flash = $flash ?? null;
        if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : ($flash['type'] === 'error' ? 'danger' : 'info') ?> alert-dismissible fade show mx-3 mt-3" role="alert">
            <?= htmlspecialchars($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Zone de contenu -->
        <main class="main-content">
            <?= $content ?>
        </main>

        <footer class="text-center text-muted py-3 small">
            SGE v<?= APP_VERSION ?> &mdash; Université de Lomé &mdash; <?= date('Y') ?>
        </footer>
    </div>
    <!-- ===== FIN CONTENU PRINCIPAL ===== -->

</div>

<!-- Bootstrap JS -->
<script src="<?= BASE_URL ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- JS principal -->
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
<script>
// Recherche globale temps réel
const searchInput    = document.getElementById('global-search');
const searchDropdown = document.getElementById('search-dropdown');
let searchTimer = null;

if (searchInput) {
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimer);
        const q = this.value.trim();
        if (q.length < 2) { searchDropdown.style.display = 'none'; return; }
        searchTimer = setTimeout(() => lancerRecherche(q), 300);
    });

    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            window.location.href = '<?= BASE_URL ?>/recherche?q=' + encodeURIComponent(this.value.trim());
        }
        if (e.key === 'Escape') {
            searchDropdown.style.display = 'none';
        }
    });

    document.addEventListener('click', e => {
        if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
            searchDropdown.style.display = 'none';
        }
    });
}

async function lancerRecherche(q) {
    try {
        const data = await fetchJSON('<?= BASE_URL ?>/recherche/api?q=' + encodeURIComponent(q));
        afficherResultats(data.results, q);
    } catch(e) {
        searchDropdown.style.display = 'none';
    }
}

function afficherResultats(results, q) {
    if (!results || results.length === 0) {
        searchDropdown.innerHTML = '<div class="p-3 text-muted text-center small">Aucun résultat pour "' + q + '"</div>';
        searchDropdown.style.display = 'block';
        return;
    }

    const iconColors = { eleve: 'text-primary', classe: 'text-success', paiement: 'text-warning' };
    let html = '';
    results.forEach(r => {
        html += `
        <a href="${r.url}" class="d-flex align-items-center gap-2 px-3 py-2 text-decoration-none
                  border-bottom text-dark" style="transition:background .15s"
           onmouseover="this.style.background='#f8f9fa'"
           onmouseout="this.style.background=''">
            <i class="bi bi-${r.icon} ${iconColors[r.type] || 'text-secondary'} fs-5"></i>
            <div class="flex-fill">
                <div class="small fw-semibold">${r.label}</div>
                <div style="font-size:.75rem" class="text-muted">${r.sub}</div>
            </div>
        </a>`;
    });

    html += `<a href="<?= BASE_URL ?>/recherche?q=${encodeURIComponent(q)}"
               class="d-block text-center small p-2 text-primary border-top">
               Voir tous les résultats →
             </a>`;

    searchDropdown.innerHTML = html;
    searchDropdown.style.display = 'block';
}
</script>
</body>
</html>
