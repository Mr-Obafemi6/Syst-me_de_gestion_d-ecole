<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'SGE') ?> — SGE</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendor/bootstrap/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendor/bootstrap-icons/css/bootstrap-icons.min.css">
    <!-- CSS principal -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css">
</head>
<body>
<div id="sidebar-overlay"></div>

<div class="d-flex" id="wrapper">

    <!-- ===== SIDEBAR ===== -->
    <nav id="sidebar">
        <!-- Logo / Nom école -->
        <div class="sidebar-brand">
            <?php
                $logoToShow = !empty($app_logo) ? $app_logo : null;
                $secondLogoPath = defined('DEFAULT_SECOND_LOGO') ? DEFAULT_SECOND_LOGO : null;
                $secondLogoToShow = ($secondLogoPath && file_exists(ROOT_PATH . '/public/' . $secondLogoPath)) ? $secondLogoPath : null;
            ?>
            <?php if (!empty($logoToShow)): ?>
                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($logoToShow) ?>" alt="Logo">
            <?php else: ?>
                <i class="bi bi-mortarboard-fill"></i>
            <?php endif; ?>
            <span><?= htmlspecialchars($app_name ?? 'SGE') ?></span>
            <?php if (!empty($secondLogoToShow)): ?>
                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($secondLogoToShow) ?>" alt="Logo secondaire" style="margin-left:8px;">
            <?php endif; ?>
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

            <li class="nav-label">GESTION ACADÉMIQUE</li>

            <li class="<?= Router::is('classes') ? 'active' : '' ?>">
                <a href="<?= Router::url('classes') ?>">
                    <i class="bi bi-building"></i> Classes
                </a>
            </li>

            <li class="<?= Router::is('matieres') ? 'active' : '' ?>">
                <a href="<?= Router::url('matieres') ?>">
                    <i class="bi bi-journal-bookmark"></i> Matières
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

            <li class="<?= Router::is('evenements') ? 'active' : '' ?>">
                <a href="<?= Router::url('evenements') ?>">
                    <i class="bi bi-calendar-event"></i> Événements
                </a>
            </li>

            <li class="<?= Router::is('export') ? 'active' : '' ?>">
                <a href="<?= Router::url('export') ?>">
                    <i class="bi bi-download"></i> Export CSV
                </a>
            </li>
            <li class="<?= Router::is('export') && ($_GET['url'] ?? '') !== 'export' ? 'active' : '' ?>">
                <a href="<?= Router::url('export/rapports') ?>">
                    <i class="bi bi-file-earmark-text-fill"></i> Rapports
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

            <?php if (AuthMiddleware::hasRole(ROLE_PARENT)): ?>
            <li class="nav-label">MON ESPACE</li>

            <li class="<?= Router::is('espace-parent') ? 'active' : '' ?>">
                <a href="<?= Router::url('espace-parent') ?>">
                    <i class="bi bi-people-fill"></i> Mes enfants
                </a>
            </li>
            <li class="<?= Router::is('notifications') ? 'active' : '' ?>">
                <a href="<?= Router::url('notifications') ?>">
                    <i class="bi bi-bell-fill"></i> Notifications
                </a>
            </li>
            <?php endif; ?>

            <?php if (AuthMiddleware::hasRole(ROLE_ELEVE)): ?>
            <li class="nav-label">MON ESPACE</li>

            <li class="<?= Router::is('espace-eleve') ? 'active' : '' ?>">
                <a href="<?= Router::url('espace-eleve') ?>">
                    <i class="bi bi-house-door-fill"></i> Mon profil
                </a>
            </li>
            <li class="<?= Router::is('espace-eleve') && ($_GET['url'] ?? '') === 'espace-eleve/paiements' ? 'active' : '' ?>">
                <a href="<?= Router::url('espace-eleve/paiements') ?>">
                    <i class="bi bi-cash-coin"></i> Mes paiements
                </a>
            </li>
            <li class="<?= Router::is('notifications') ? 'active' : '' ?>">
                <a href="<?= Router::url('notifications') ?>">
                    <i class="bi bi-bell-fill"></i> Notifications
                </a>
            </li>
            <?php endif; ?>
        </ul>

        <!-- Infos utilisateur bas de sidebar -->
        <div class="sidebar-footer">
            <a href="<?= Router::url('auth/profil') ?>" class="sidebar-user text-decoration-none">
                <?php if (!empty($current_user_photo)): ?>
                    <img src="<?= BASE_URL ?>/<?= htmlspecialchars($current_user_photo) ?>" alt="Profil" class="rounded-circle" style="width:38px;height:38px;object-fit:cover;">
                <?php else: ?>
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:38px;height:38px;font-size:.9rem;font-weight:700;">
                        <?= htmlspecialchars($current_user_initials ?? 'SG') ?>
                    </div>
                <?php endif; ?>
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
            <div class="topbar-left">
                <button id="sidebarToggle" class="icon-button" title="Réduire/Agrandir le menu">
                    <i class="bi bi-list"></i>
                </button>

                <div class="brand-pill">
                    <div class="brand-mark">
                        <?php
                            $topbarLogo = !empty($app_logo) ? $app_logo : null;
                        ?>
                        <?php if (!empty($topbarLogo)): ?>
                            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($topbarLogo) ?>" alt="Logo" style="width:36px;height:36px;object-fit:contain;">
                        <?php else: ?>
                            <i class="bi bi-mortarboard-fill"></i>
                        <?php endif; ?>
                    </div>
                    <div class="brand-meta">
                        <span class="brand-name"><?= htmlspecialchars($app_name ?? 'SGE') ?></span>
                        <span class="brand-subtitle">Établissement</span>
                    </div>
                </div>
            </div>

            <div class="topbar-title d-none d-lg-block">
                <?= htmlspecialchars($pageTitle ?? 'Tableau de bord') ?>
            </div>

            <div class="header-search">
                <i class="bi bi-search"></i>
                <input type="text" id="global-search"
                        placeholder="Rechercher élève, classe, reçu..."
                        autocomplete="off">
                <div id="search-dropdown"
                        class="position-absolute w-100 bg-white border rounded shadow"
                        style="z-index:1050;display:none;max-height:320px;overflow-y:auto;top:calc(100% + 8px);left:0">
                    </div>
            </div>

            <div class="topbar-actions">
                <div id="header-clock" class="header-clock d-none d-xl-flex"></div>

                <div class="position-relative">
                    <button type="button" id="notification-toggle" class="icon-button position-relative" title="Notifications" data-badge-url="<?= Router::url('notifications/getUnreadBadge') ?>">
                        <i class="bi bi-bell"></i>
                        <span id="notification-badge" class="notification-pill" style="display:none;">
                            <span id="notification-count">0</span>
                        </span>
                    </button>
                    <div id="notification-dropdown" class="notif-dropdown" style="display:none">
                        <div class="notif-dropdown-header">
                            <span>Notifications</span>
                            <button type="button" id="notif-mark-all" class="btn-link-sm">Tout marquer lu</button>
                        </div>
                        <div id="notif-dropdown-body" class="notif-dropdown-body">
                            <div class="text-center text-muted small py-4">Chargement…</div>
                        </div>
                        <a href="<?= Router::url('notifications') ?>" class="notif-dropdown-footer">Voir toutes les notifications</a>
                    </div>
                </div>

                <div class="dropdown profile-dropdown">
                    <button type="button" class="profile-trigger dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php if (!empty($current_user_photo)): ?>
                            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($current_user_photo) ?>" alt="Profil" class="profile-avatar" style="width:44px;height:44px;object-fit:cover;">
                        <?php else: ?>
                            <div class="profile-avatar"><?= htmlspecialchars($current_user_initials ?? 'SG') ?></div>
                        <?php endif; ?>
                        <div class="profile-meta d-none d-xl-block">
                            <span class="profile-name"><?= htmlspecialchars(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?></span>
                            <span class="profile-role"><?= htmlspecialchars(ucfirst($user['role'] ?? '')) ?></span>
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><a class="dropdown-item" href="<?= Router::url('parametres') ?>"><i class="bi bi-gear me-2"></i>Paramètres</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= Router::url('auth/logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Déconnexion</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <div id="welcome-toast" class="welcome-toast" role="status" aria-live="polite"></div>

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
            <?= $content ?? '' ?>
        </main>

        <footer class="text-center text-muted py-3 small">
            SGE v<?= APP_VERSION ?> &mdash; <?= date('Y') ?>
        </footer>
    </div>
    <!-- ===== FIN CONTENU PRINCIPAL ===== -->

</div>

<!-- Bootstrap JS -->
<script src="<?= BASE_URL ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- JS principal -->
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
<script>
const CSRF_TOKEN = '<?= htmlspecialchars($csrf_token ?? '') ?>';

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
           onmouseover="this.style.background='#e8f0fb'"
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

// ── Dropdown notifications ──
const notifToggle   = document.getElementById('notification-toggle');
const notifDropdown = document.getElementById('notification-dropdown');
const notifBody     = document.getElementById('notif-dropdown-body');
const notifMarkAll  = document.getElementById('notif-mark-all');

function timeAgo(dateStr) {
    const diffSec = Math.floor((Date.now() - new Date(dateStr.replace(' ', 'T'))) / 1000);
    if (diffSec < 60) return 'à l\'instant';
    if (diffSec < 3600) return Math.floor(diffSec / 60) + ' min';
    if (diffSec < 86400) return Math.floor(diffSec / 3600) + ' h';
    return Math.floor(diffSec / 86400) + ' j';
}

async function chargerNotifications() {
    if (!notifBody) return;
    try {
        const data = await fetchJSON('<?= Router::url('notifications/getUnreadBadge') ?>');
        const recent = data.recent || [];
        if (recent.length === 0) {
            notifBody.innerHTML = '<div class="text-center text-muted small py-4"><i class="bi bi-bell-slash fs-4 d-block mb-2"></i>Aucune nouvelle notification</div>';
            return;
        }
        notifBody.innerHTML = recent.map(n => `
            <div class="notif-item">
                <div class="notif-item-title">${n.title}</div>
                <div class="notif-item-body">${n.body}</div>
                <div class="notif-item-time">${timeAgo(n.created_at)}</div>
            </div>
        `).join('');
    } catch (e) {
        notifBody.innerHTML = '<div class="text-center text-muted small py-4">Erreur de chargement</div>';
    }
}

if (notifToggle) {
    notifToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        const isOpen = notifDropdown.style.display === 'block';
        notifDropdown.style.display = isOpen ? 'none' : 'block';
        if (!isOpen) chargerNotifications();
    });

    document.addEventListener('click', (e) => {
        if (!notifDropdown.contains(e.target) && !notifToggle.contains(e.target)) {
            notifDropdown.style.display = 'none';
        }
    });
}

if (notifMarkAll) {
    notifMarkAll.addEventListener('click', async () => {
        try {
            await fetch('<?= Router::url('notifications/marquer-tout-lu') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'csrf_token=' + encodeURIComponent(CSRF_TOKEN)
            });
            chargerNotifications();
            document.getElementById('notification-badge').style.display = 'none';
        } catch (e) {}
    });
}

</script>
</body>
</html>
