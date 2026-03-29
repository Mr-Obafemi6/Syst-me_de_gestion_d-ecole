// public/assets/js/app.js — Scripts globaux SGE v1.0

document.addEventListener('DOMContentLoaded', () => {

    // ===== TOGGLE SIDEBAR =====
    const sidebar  = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebar_collapsed', sidebar.classList.contains('collapsed'));
        });
        if (localStorage.getItem('sidebar_collapsed') === 'true') {
            sidebar.classList.add('collapsed');
        }
    }

    // ===== AUTO-FERMETURE DES ALERTES FLASH =====
    document.querySelectorAll('.alert-dismissible').forEach(alert => {
        setTimeout(() => {
            try { bootstrap.Alert.getOrCreateInstance(alert).close(); } catch(e) {}
        }, 4000);
    });

    // ===== CONFIRMATION DE SUPPRESSION =====
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', e => {
            if (!confirm(el.dataset.confirm || 'Confirmer cette action ?')) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    });

    // ===== TOOLTIPS BOOTSTRAP =====
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el);
    });

    // ===== TOGGLE VISIBILITÉ MOT DE PASSE =====
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

    // ===== INDICATEUR CHARGEMENT SUR SOUMISSION FORMULAIRE =====
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', () => {
            const btn = form.querySelector('[type="submit"]');
            if (btn && !btn.dataset.noloader) {
                btn.disabled = true;
                const orig = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Chargement...';
                // Réactiver après 8s en cas d'erreur
                setTimeout(() => {
                    btn.disabled = false;
                    btn.innerHTML = orig;
                }, 8000);
            }
        });
    });

    // ===== RECHERCHE AVEC DEBOUNCE =====
    const searchInputs = document.querySelectorAll('input[data-search-form]');
    searchInputs.forEach(input => {
        let timer = null;
        input.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(() => {
                input.closest('form')?.submit();
            }, 500);
        });
    });

});

// ===== UTILITAIRES GLOBAUX =====

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
        throw new Error(err.error || `Erreur HTTP ${response.status}`);
    }
    return response.json();
}

/**
 * Formate un montant en FCFA
 */
function formatFCFA(amount) {
    return new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount) + ' FCFA';
}

/**
 * Affiche un toast Bootstrap
 */
function showToast(message, type = 'success') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        container.style.zIndex = '1100';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-bg-${type} border-0 show`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"></button>
        </div>`;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
}

/**
 * Retourne la couleur CSS selon une moyenne /20
 */
function couleurMoyenne(moy) {
    if (moy >= 14) return 'text-success';
    if (moy >= 10) return 'text-primary';
    if (moy >= 8)  return 'text-warning';
    return 'text-danger';
}

/**
 * Retourne la mention selon la moyenne
 */
function getMention(moy) {
    if (moy >= 16) return 'Très bien';
    if (moy >= 14) return 'Bien';
    if (moy >= 12) return 'Assez bien';
    if (moy >= 10) return 'Passable';
    return moy > 0 ? 'Insuffisant' : '—';
}
