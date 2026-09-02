// public/assets/js/app.js — SGE v2 — Chargement + UX améliorés

/* ===== LOADER GLOBAL ===== */
(function () {
    const loader = document.createElement('div');
    loader.id = 'page-loader';
    loader.innerHTML = `
        <div class="loader-shell">
            <div class="loader-brand">
                <div class="loader-brand-badge">SGE</div>
                <div>
                    <div class="loader-brand-name">SGE</div>
                    <div class="loader-brand-sub">Gestion scolaire</div>
                </div>
            </div>
            <div class="loader-ring"></div>
            <div class="loader-text">Chargement des données...</div>
            <div class="loader-subtext">Préparation de votre espace</div>
        </div>
    `;
    document.documentElement.appendChild(loader);

    const bar = document.createElement('div');
    bar.id = 'nprogress-bar';
    bar.style.width = '0';
    document.documentElement.appendChild(bar);

    let prog = 0;
    const iv = setInterval(() => {
        prog = prog < 85 ? prog + (85 - prog) * 0.08 : prog;
        bar.style.width = prog + '%';
    }, 100);

    function hideLoader() {
        clearInterval(iv);
        bar.style.width = '100%';
        setTimeout(() => {
            loader.classList.add('hidden');
            bar.style.opacity = '0';
            setTimeout(() => bar.remove(), 400);
        }, 220);
    }

    if (document.readyState === 'complete') {
        setTimeout(hideLoader, 650);
    } else {
        window.addEventListener('load', () => setTimeout(hideLoader, 650));
    }

    document.addEventListener('click', function (e) {
        const a = e.target.closest('a[href]');
        if (!a) return;
        const href = a.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript') ||
            a.target === '_blank' || e.ctrlKey || e.metaKey) return;
        loader.classList.remove('hidden');
        loader.querySelector('.loader-text').textContent = 'Navigation…';
        loader.querySelector('.loader-subtext').textContent = 'Chargement de la page';
        const bar2 = document.createElement('div');
        bar2.id = 'nprogress-bar';
        bar2.style.width = '20%';
        document.documentElement.appendChild(bar2);
        let p2 = 20;
        setInterval(() => { p2 = p2 < 80 ? p2 + (80 - p2) * 0.1 : p2; bar2.style.width = p2 + '%'; }, 120);
    });
})();

/* ===== DOM READY ===== */
document.addEventListener('DOMContentLoaded', () => {
    const sidebar   = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const overlay = document.getElementById('sidebar-overlay');
    const toast = document.getElementById('welcome-toast');
    const clockEl = document.getElementById('header-clock');

    function updateClock() {
        if (clockEl) {
            const now = new Date();
            clockEl.textContent = now.toLocaleString('fr-FR', { weekday: 'short', day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' });
        }
    }
    updateClock();
    setInterval(updateClock, 30000);

    function toggleSidebar(force) {
        if (!sidebar) return;
        const shouldOpen = force ?? !sidebar.classList.contains('mobile-open');
        sidebar.classList.toggle('mobile-open', shouldOpen);
        document.body.classList.toggle('sidebar-open', shouldOpen && window.innerWidth < 992);
    }

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
            if (window.innerWidth < 992) {
                toggleSidebar();
            } else {
                sidebar.classList.toggle('collapsed');
                localStorage.setItem('sidebar_collapsed', sidebar.classList.contains('collapsed'));
            }
        });
        if (localStorage.getItem('sidebar_collapsed') === 'true') {
            sidebar.classList.add('collapsed');
        }
    }

    if (overlay) {
        overlay.addEventListener('click', () => toggleSidebar(false));
    }

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992) {
            document.body.classList.remove('sidebar-open');
            sidebar?.classList.remove('mobile-open');
        }
    });

    function showWelcomeMessage() {
        if (!toast) return;
        const path = (window.location.pathname || '').split('/').filter(Boolean)[0] || 'dashboard';
        const route = path.toLowerCase();
        const messages = {
            dashboard: { icon: '👋', title: 'Bonjour, SGE Admin', body: 'Bienvenue sur votre tableau de bord.' },
            eleves: { icon: '📚', title: 'Bienvenue dans le module Élèves', body: 'Gérez les inscriptions et le suivi des élèves.' },
            classes: { icon: '🏫', title: 'Bienvenue dans le module Classes', body: 'Organisez vos classes et leurs responsables.' },
            notes: { icon: '📝', title: 'Bienvenue dans le module Notes', body: 'Saisissez et consultez les évaluations.' },
            bulletins: { icon: '📄', title: 'Bienvenue dans le module Bulletins', body: 'Générez les bulletins scolaires.' },
            absences: { icon: '🚫', title: 'Bienvenue dans le module Absences', body: 'Consultez les absences et justificatifs.' },
            evenements: { icon: '📅', title: 'Bienvenue dans le module Événements', body: 'Planifiez les activités de votre établissement.' },
            paiements: { icon: '💰', title: 'Bienvenue dans le module Paiements', body: 'Consultez les recettes et encaissements.' },
            parametres: { icon: '⚙️', title: 'Bienvenue dans les paramètres', body: 'Configurez votre application.' }
        };
        const msg = messages[route] || messages.dashboard;
        const today = new Date().toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        toast.innerHTML = `
            <div class="toast-icon">${msg.icon}</div>
            <div>
                <div class="toast-title">${msg.title}</div>
                <div class="toast-body">${msg.body}<br>${today}</div>
            </div>`;
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 4000);
    }

    setTimeout(showWelcomeMessage, 900);

    // ── AUTO-FERMETURE ALERTES FLASH ──
    document.querySelectorAll('.alert-dismissible').forEach(alert => {
        setTimeout(() => {
            try { bootstrap.Alert.getOrCreateInstance(alert).close(); } catch (e) {}
        }, 4000);
    });

    // ── CONFIRMATION SUPPRESSION ──
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', e => {
            if (!confirm(el.dataset.confirm || 'Confirmer cette action ?')) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    });

    // ── TOOLTIPS BOOTSTRAP ──
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el);
    });

    // ── TOGGLE MOT DE PASSE ──
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

    // ── CHARGEMENT SUR SOUMISSION FORMULAIRE (bouton avec spinner) ──
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function () {
            const btn = form.querySelector('[type="submit"]');
            if (btn && !btn.dataset.noloader) {
                btn.disabled = true;
                btn.classList.add('btn-loading');
                const orig = btn.innerHTML;
                btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Patientez…`;
                // Sécurité : réactiver après 10s
                setTimeout(() => {
                    btn.disabled = false;
                    btn.classList.remove('btn-loading');
                    btn.innerHTML = orig;
                }, 10000);
            }
        });
    });

    // ── RECHERCHE AVEC DEBOUNCE ──
    document.querySelectorAll('input[data-search-form]').forEach(input => {
        let timer = null;
        input.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(() => input.closest('form')?.submit(), 500);
        });
    });

    // ── BADGE NOTIFICATIONS ──
    function updateNotificationBadge() {
        const badge = document.getElementById('notification-badge');
        const count = document.getElementById('notification-count');
        if (!badge || !count) return;
        fetch(badge.closest('a')?.dataset.badgeUrl || '#')
            .then(r => r.json())
            .then(data => {
                if (data.unread_count > 0) {
                    count.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }
            })
            .catch(() => {});
    }
    updateNotificationBadge();
    setInterval(updateNotificationBadge, 30000);

});

/* ===== UTILITAIRES GLOBAUX ===== */

/**
 * Fetch JSON avec gestion d'erreur centralisée
 */
async function fetchJSON(url, options = {}) {
    options.headers = {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        ...(options.headers || {})
    };
    const response = await fetch(url, options);
    if (!response.ok) {
        const err = await response.json().catch(() => ({}));
        throw new Error(err.message || `HTTP ${response.status}`);
    }
    return response.json();
}
