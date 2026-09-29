<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'SGE') ?> — SGE (Systemegestionecol)</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendor/bootstrap/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendor/bootstrap-icons/css/bootstrap-icons.min.css">
    <!-- CSS principal -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css?v=<?= filemtime(ROOT_PATH . '/public/assets/css/app.css') ?>">
</head>
<body>
<div id="sidebar-overlay"></div>

<div class="d-flex" id="wrapper">

    <!-- ===== SIDEBAR ===== -->
    <nav id="sidebar">
        <?php
            $sidebarAuth = AuthorizationService::getInstance();
            $hasSettings = $sidebarAuth->hasPermission('settings.view');
            $hasDocumentation = false;
            $hasSupport = false;
            $activeYear = trim((string) ($active_school_year ?? ''));
            $schoolLogo = !empty($app_logo) ? $app_logo : null;
            $userFullName = trim((($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')));
            $userRole = ucfirst($user['role'] ?? '');
        ?>

        <div class="sidebar-header">
            <span>SystemeGestionEcole</span>
        </div>

        <div class="sidebar-establishment">
            <button type="button" class="sidebar-establishment-toggle" aria-expanded="false" aria-controls="sidebar-user-menu">
                <div class="sidebar-establishment-main">
                    <?php if (!empty($schoolLogo)): ?>
                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($schoolLogo) ?>" alt="Logo établissement" class="sidebar-establishment-logo">
                    <?php else: ?>
                        <div class="sidebar-establishment-logo icon-shell">
                            <i class="bi bi-mortarboard-fill"></i>
                        </div>
                    <?php endif; ?>
                    <div class="sidebar-establishment-text">
                        <div class="sidebar-establishment-name"><?= htmlspecialchars($app_name ?? 'SGE') ?></div>
                        <?php if (!empty($activeYear)): ?>
                            <div class="sidebar-establishment-detail"><?= htmlspecialchars($activeYear) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <span class="sidebar-establishment-chevron"><i class="bi bi-chevron-down"></i></span>
            </button>

            <div id="sidebar-user-menu" class="sidebar-user-menu" aria-hidden="true">
                <div class="sidebar-user-summary">
                    <?php if (!empty($current_user_photo)): ?>
                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($current_user_photo) ?>" alt="Photo profil" class="sidebar-user-avatar">
                    <?php else: ?>
                        <div class="sidebar-user-avatar initials"><?= htmlspecialchars($current_user_initials ?? 'SG') ?></div>
                    <?php endif; ?>
                    <div class="sidebar-user-meta">
                        <span class="sidebar-user-name"><?= htmlspecialchars($userFullName ?: 'Utilisateur') ?></span>
                        <span class="sidebar-user-role"><?= htmlspecialchars($userRole ?: 'Utilisateur') ?></span>
                    </div>
                </div>

                <a href="<?= Router::url('auth/profil') ?>" class="sidebar-user-link">
                    <i class="bi bi-person-circle"></i>
                    <span>Profil</span>
                </a>

                <?php if ($hasDocumentation): ?>
                    <a href="<?= Router::url('documentation') ?>" class="sidebar-user-link">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Documentation</span>
                    </a>
                <?php else: ?>
                    <div class="sidebar-user-link disabled" aria-disabled="true">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Documentation</span>
                        <small class="sidebar-user-note">Aucune route existante</small>
                    </div>
                <?php endif; ?>

                <?php if ($hasSupport): ?>
                    <a href="<?= Router::url('support') ?>" class="sidebar-user-link">
                        <i class="bi bi-life-preserver"></i>
                        <span>Support</span>
                    </a>
                <?php else: ?>
                    <div class="sidebar-user-link disabled" aria-disabled="true">
                        <i class="bi bi-life-preserver"></i>
                        <span>Support</span>
                        <small class="sidebar-user-note">Aucune route existante</small>
                    </div>
                <?php endif; ?>

                <?php if ($hasSettings): ?>
                    <a href="<?= Router::url('parametres') ?>" class="sidebar-user-link">
                        <i class="bi bi-gear"></i>
                        <span>Paramètres</span>
                    </a>
                <?php endif; ?>

                <a href="<?= Router::url('auth/logout') ?>" class="sidebar-user-link danger">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Déconnexion</span>
                </a>
            </div>
        </div>

        <div class="sidebar-menu-scroll">
            <ul class="sidebar-nav">
                <?php if ($sidebarAuth->hasPermission('dashboard.view')): ?>
                <li class="nav-label">PRINCIPAL</li>
                <li class="<?= Router::is('dashboard') ? 'active' : '' ?>">
                    <a href="<?= Router::url('dashboard') ?>" title="Tableau de bord">
                        <i class="bi bi-speedometer2"></i><span>Tableau de bord</span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if ($sidebarAuth->hasPermission('students.view') || $sidebarAuth->hasPermission('classes.view') || $sidebarAuth->hasPermission('subjects.view')): ?>
                <li class="nav-label">SCOLARITÉ</li>
                <?php if ($sidebarAuth->hasPermission('students.view')): ?><li class="<?= Router::is('eleves') ? 'active' : '' ?>">
                    <a href="<?= Router::url('eleves') ?>" title="Élèves"><i class="bi bi-people-fill"></i><span>Élèves</span></a>
                </li><?php endif; ?>
                <?php if ($sidebarAuth->hasPermission('classes.view')): ?><li class="<?= Router::is('classes') ? 'active' : '' ?>">
                    <a href="<?= Router::url('classes') ?>" title="Classes"><i class="bi bi-building"></i><span>Classes</span></a>
                </li><?php endif; ?>
                <?php if ($sidebarAuth->hasPermission('subjects.view')): ?><li class="<?= Router::is('matieres') ? 'active' : '' ?>">
                    <a href="<?= Router::url('matieres') ?>" title="Matières"><i class="bi bi-journal-bookmark"></i><span>Matières</span></a>
                </li><?php endif; ?>
                <?php endif; ?>

                <?php if ($sidebarAuth->hasPermission('grades.view') || $sidebarAuth->hasPermission('report_cards.view') || $sidebarAuth->hasPermission('attendance.view')): ?>
                <li class="nav-label">ÉVALUATION</li>
                <?php if ($sidebarAuth->hasPermission('grades.view')): ?><li class="<?= Router::is('notes') ? 'active' : '' ?>">
                    <a href="<?= Router::url('notes') ?>" title="Notes"><i class="bi bi-pencil-square"></i><span>Notes</span></a>
                </li><?php endif; ?>
                <?php if ($sidebarAuth->hasPermission('report_cards.view')): ?><li class="<?= Router::is('bulletins') ? 'active' : '' ?>">
                    <a href="<?= Router::url('bulletins') ?>" title="Bulletins"><i class="bi bi-file-earmark-text"></i><span>Bulletins</span></a>
                </li><?php endif; ?>
                <?php if ($sidebarAuth->hasPermission('attendance.view')): ?><li class="<?= Router::is('absences') ? 'active' : '' ?>">
                    <a href="<?= Router::url('absences') ?>" title="Absences"><i class="bi bi-calendar-x"></i><span>Absences</span></a>
                </li><?php endif; ?>
                <?php endif; ?>

                <?php if ($sidebarAuth->hasPermission('events.view')): ?>
                <li class="nav-label">ORGANISATION</li>
                <li class="<?= Router::is('evenements') ? 'active' : '' ?>">
                    <a href="<?= Router::url('evenements') ?>" title="Événements"><i class="bi bi-calendar-event"></i><span>Événements</span></a>
                </li>
                <?php endif; ?>

                <?php if ($sidebarAuth->hasPermission('notifications.view') || AuthMiddleware::hasRole(ROLE_PARENT) || AuthMiddleware::hasRole(ROLE_ELEVE)): ?>
                <li class="nav-label">COMMUNICATION</li>
                <li class="<?= Router::is('notifications') ? 'active' : '' ?>">
                    <a href="<?= Router::url('notifications') ?>" title="Notifications"><i class="bi bi-bell-fill"></i><span>Notifications</span></a>
                </li>
                <?php endif; ?>

                <?php if ($sidebarAuth->hasPermission('finance.view')): ?>
                <li class="nav-label">FINANCES</li>
                <li class="<?= Router::is('paiements') ? 'active' : '' ?>">
                    <a href="<?= Router::url('paiements') ?>" title="Paiements"><i class="bi bi-cash-coin"></i><span>Paiements</span></a>
                </li>
                <?php endif; ?>

                <?php if ($sidebarAuth->hasPermission('report_cards.export')): ?>
                <li class="nav-label">RAPPORTS</li>
                <li class="<?= Router::is('export') && ($_GET['url'] ?? '') === 'export' ? 'active' : '' ?>">
                    <a href="<?= Router::url('export') ?>" title="Export CSV"><i class="bi bi-download"></i><span>Export CSV</span></a>
                </li>
                <li class="<?= Router::is('export') && ($_GET['url'] ?? '') !== 'export' ? 'active' : '' ?>">
                    <a href="<?= Router::url('export/rapports') ?>" title="Rapports"><i class="bi bi-file-earmark-text-fill"></i><span>Rapports</span></a>
                </li>
                <?php endif; ?>

                <?php if ($sidebarAuth->hasPermission('teachers.view') || $sidebarAuth->hasPermission('schedule.view') || $sidebarAuth->hasPermission('permissions.view') || $sidebarAuth->hasPermission('assignments.view')): ?>
                <li class="nav-label">ADMINISTRATION</li>
                <?php if ($sidebarAuth->hasPermission('teachers.view')): ?><li class="<?= Router::is('enseignants') ? 'active' : '' ?>">
                    <a href="<?= Router::url('enseignants') ?>" title="Enseignants"><i class="bi bi-person-badge-fill"></i><span>Enseignants</span></a>
                </li><?php endif; ?>
                <?php if ($sidebarAuth->hasPermission('schedule.view')): ?><li class="<?= Router::is('emplois-du-temps') ? 'active' : '' ?>">
                    <a href="<?= Router::url('emplois-du-temps') ?>" title="Emploi du temps"><i class="bi bi-calendar3"></i><span>Emploi du temps</span></a>
                </li><?php endif; ?>
                <?php if ($sidebarAuth->hasPermission('permissions.view')): ?><li class="<?= Router::is('permissions') ? 'active' : '' ?>">
                    <a href="<?= Router::url('permissions') ?>" title="Rôles &amp; permissions"><i class="bi bi-shield-lock-fill"></i><span>Rôles &amp; permissions</span></a>
                </li><?php endif; ?>
                <?php if ($sidebarAuth->hasPermission('assignments.view')): ?><li class="<?= Router::is('affectations') ? 'active' : '' ?>">
                    <a href="<?= Router::url('affectations') ?>" title="Affectations enseignants"><i class="bi bi-person-workspace"></i><span>Affectations enseignants</span></a>
                </li><?php endif; ?>
                <?php endif; ?>

                <?php if ($sidebarAuth->hasPermission('documentation.view') || $sidebarAuth->hasPermission('schedule.view')): ?>
                <li class="nav-label">OUTILS</li>
                <?php if ($sidebarAuth->hasPermission('documentation.view')): ?><li class="<?= Router::is('documentation') ? 'active' : '' ?>">
                    <a href="<?= Router::url('documentation') ?>" title="Documentation"><i class="bi bi-journal-text"></i><span>Documentation</span></a>
                </li><?php endif; ?>
                <?php endif; ?>

                <?php if (AuthMiddleware::hasRole(ROLE_PARENT)): ?>
                <li class="nav-label">MON ESPACE</li>
                <li class="<?= Router::is('espace-parent') ? 'active' : '' ?>">
                    <a href="<?= Router::url('espace-parent') ?>" title="Mes enfants"><i class="bi bi-people-fill"></i><span>Mes enfants</span></a>
                </li>
                <li class="<?= Router::is('espace-parent') && ($_GET['url'] ?? '') === 'espace-parent/paiements' ? 'active' : '' ?>">
                    <a href="<?= Router::url('espace-parent/paiements') ?>" title="Mes paiements"><i class="bi bi-cash-coin"></i><span>Mes paiements</span></a>
                </li>
                <?php endif; ?>

                <?php if (AuthMiddleware::hasRole(ROLE_ELEVE)): ?>
                <li class="nav-label">MON ESPACE</li>
                <li class="<?= Router::is('espace-eleve') && ($_GET['url'] ?? '') === 'espace-eleve' ? 'active' : '' ?>">
                    <a href="<?= Router::url('espace-eleve') ?>" title="Mon profil"><i class="bi bi-house-door-fill"></i><span>Mon profil</span></a>
                </li>
                <li class="<?= Router::is('espace-eleve') && ($_GET['url'] ?? '') === 'espace-eleve/paiements' ? 'active' : '' ?>">
                    <a href="<?= Router::url('espace-eleve/paiements') ?>" title="Mes paiements"><i class="bi bi-cash-coin"></i><span>Mes paiements</span></a>
                </li>
                <?php endif; ?>
            </ul>
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

function esc(v) {
    return String(v ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
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
        searchDropdown.innerHTML = '<div class="p-3 text-muted text-center small">Aucun résultat pour "' + esc(q) + '"</div>';
        searchDropdown.style.display = 'block';
        return;
    }

    const iconColors = { eleve: 'text-primary', classe: 'text-success', paiement: 'text-warning' };
    let html = '';
    results.forEach(r => {
        html += `
        <a href="${esc(r.url)}" class="d-flex align-items-center gap-2 px-3 py-2 text-decoration-none
                  border-bottom text-dark" style="transition:background .15s"
           onmouseover="this.style.background='#e8f0fb'"
           onmouseout="this.style.background=''">
            <i class="bi bi-${esc(r.icon)} ${iconColors[r.type] || 'text-secondary'} fs-5"></i>
            <div class="flex-fill">
                <div class="small fw-semibold">${esc(r.label)}</div>
                <div style="font-size:.75rem" class="text-muted">${esc(r.sub)}</div>
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
                <div class="notif-item-title">${esc(n.title)}</div>
                <div class="notif-item-body">${esc(n.body)}</div>
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
