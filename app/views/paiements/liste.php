<!-- app/views/paiements/liste.php -->
<?php $p = $pagination; ?>

<!-- KPI Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="kpi-card kpi-green">
            <div>
                <div class="kpi-value"><?= number_format($stats['total_paye'] ?? 0, 0, ',', ' ') ?></div>
                <div class="kpi-label">FCFA encaissés</div>
            </div>
            <i class="bi bi-cash-coin kpi-icon"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card kpi-blue">
            <div>
                <div class="kpi-value"><?= $stats['nb_paiements'] ?? 0 ?></div>
                <div class="kpi-label">Paiements enregistrés</div>
            </div>
            <i class="bi bi-receipt kpi-icon"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card kpi-orange">
            <div>
                <div class="kpi-value"><?= number_format($stats['total_partiel'] ?? 0, 0, ',', ' ') ?></div>
                <div class="kpi-label">FCFA partiels</div>
            </div>
            <i class="bi bi-hourglass-split kpi-icon"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card kpi-red">
            <div>
                <div class="kpi-value"><?= $stats['nb_eleves_ayant_paye'] ?? 0 ?></div>
                <div class="kpi-label">Élèves ayant payé</div>
            </div>
            <i class="bi bi-people-fill kpi-icon"></i>
        </div>
    </div>
</div>

<!-- Graphique mensuel -->
<?php if (!empty($parMois)): ?>
<div class="card mb-4">
    <div class="card-header">
        <i class="bi bi-bar-chart me-1 text-primary"></i>
        Encaissements par mois — <?= htmlspecialchars($annee['libelle'] ?? '') ?>
    </div>
    <div class="card-body">
        <canvas id="chart-mois" height="80"></canvas>
    </div>
</div>
<?php endif; ?>

<!-- Filtres + bouton ajouter -->
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <form method="GET" action="<?= Router::url('paiements') ?>" class="d-flex gap-2 flex-wrap">
        <select name="statut" class="form-select form-select-sm" style="width:150px">
            <option value="">Tous les statuts</option>
            <option value="paye"    <?= $statut === 'paye'    ? 'selected' : '' ?>>Payé</option>
            <option value="partiel" <?= $statut === 'partiel' ? 'selected' : '' ?>>Partiel</option>
            <option value="annule"  <?= $statut === 'annule'  ? 'selected' : '' ?>>Annulé</option>
        </select>
        <button class="btn btn-sm btn-primary"><i class="bi bi-filter me-1"></i>Filtrer</button>
        <?php if ($statut): ?>
        <a href="<?= Router::url('paiements') ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-x"></i> Réinitialiser
        </a>
        <?php endif; ?>
    </form>
    <a href="<?= Router::url('paiements/ajouter') ?>" class="btn btn-success">
        <i class="bi bi-plus-circle-fill me-1"></i> Enregistrer un paiement
    </a>
</div>

<!-- Tableau -->
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <span><i class="bi bi-list-ul me-1 text-primary"></i> Historique des paiements</span>
        <span class="badge bg-primary"><?= $p['total'] ?> paiement<?= $p['total'] > 1 ? 's' : '' ?></span>
    </div>
    <div class="card-body p-0">
        <?php if (empty($p['data'])): ?>
        <div class="text-center text-muted py-5">
            <i class="bi bi-receipt fs-1"></i>
            <p class="mt-2">Aucun paiement enregistré.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>N° Reçu</th>
                        <th>Élève</th>
                        <th>Classe</th>
                        <th class="text-end">Montant</th>
                        <th class="text-center">Mode</th>
                        <th>Date</th>
                        <th class="text-center">Statut</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($p['data'] as $pai): ?>
                <tr>
                    <td>
                        <span class="badge bg-secondary font-monospace">
                            <?= htmlspecialchars($pai['recu_numero']) ?>
                        </span>
                    </td>
                    <td>
                        <div class="fw-semibold small">
                            <?= htmlspecialchars($pai['eleve_nom'] . ' ' . $pai['eleve_prenom']) ?>
                        </div>
                        <div class="text-muted" style="font-size:.75rem">
                            <?= htmlspecialchars($pai['matricule']) ?>
                        </div>
                    </td>
                    <td class="small"><?= htmlspecialchars($pai['classe_nom']) ?></td>
                    <td class="text-end fw-bold text-success">
                        <?= number_format($pai['montant_fcfa'], 0, ',', ' ') ?> FCFA
                    </td>
                    <td class="text-center">
                        <?php
                        $modeIcon = ['especes' => 'bi-cash', 'mobile_money' => 'bi-phone', 'virement' => 'bi-bank'];
                        $modeLabel = ['especes' => 'Espèces', 'mobile_money' => 'Mobile Money', 'virement' => 'Virement'];
                        ?>
                        <span title="<?= $modeLabel[$pai['mode_paiement']] ?? '' ?>">
                            <i class="bi <?= $modeIcon[$pai['mode_paiement']] ?? 'bi-cash' ?>"></i>
                            <span class="d-none d-lg-inline small"><?= $modeLabel[$pai['mode_paiement']] ?? '' ?></span>
                        </span>
                    </td>
                    <td class="small"><?= date('d/m/Y', strtotime($pai['date_paiement'])) ?></td>
                    <td class="text-center">
                        <?php
                        $badgeCls = ['paye' => 'success', 'partiel' => 'warning text-dark', 'annule' => 'danger'];
                        $badgeLbl = ['paye' => 'Payé', 'partiel' => 'Partiel', 'annule' => 'Annulé'];
                        ?>
                        <span class="badge bg-<?= $badgeCls[$pai['statut']] ?? 'secondary' ?>">
                            <?= $badgeLbl[$pai['statut']] ?? $pai['statut'] ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="<?= Router::url('paiements/recu/' . $pai['id']) ?>"
                           class="btn btn-sm btn-outline-primary" title="Voir le reçu">
                            <i class="bi bi-receipt"></i>
                        </a>
                        <?php if ($pai['statut'] !== 'annule'): ?>
                        <form method="POST"
                              action="<?= Router::url('paiements/annuler/' . $pai['id']) ?>"
                              class="d-inline">
                            <input type="hidden" name="csrf_token"
                                   value="<?= htmlspecialchars($csrf_token) ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    data-confirm="Annuler ce paiement ?">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($p['last_page'] > 1): ?>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <small class="text-muted">Page <?= $p['current_page'] ?> / <?= $p['last_page'] ?></small>
        <nav>
            <ul class="pagination pagination-sm mb-0">
                <?php for ($i = 1; $i <= $p['last_page']; $i++): ?>
                <li class="page-item <?= $i == $p['current_page'] ? 'active' : '' ?>">
                    <a class="page-link"
                       href="<?= Router::url('paiements?page=' . $i . ($statut ? '&statut=' . $statut : '')) ?>">
                        <?= $i ?>
                    </a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<!-- Chart.js -->
<?php if (!empty($parMois)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
const labels = <?= json_encode(array_column($parMois, 'mois_label')) ?>;
const totaux = <?= json_encode(array_map('intval', array_column($parMois, 'total'))) ?>;

new Chart(document.getElementById('chart-mois'), {
    type: 'bar',
    data: {
        labels,
        datasets: [{
            label: 'Encaissements (FCFA)',
            data: totaux,
            backgroundColor: 'rgba(15,52,96,0.75)',
            borderColor: '#0f3460',
            borderWidth: 1,
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => new Intl.NumberFormat('fr-FR').format(ctx.raw) + ' FCFA'
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: v => new Intl.NumberFormat('fr-FR').format(v)
                }
            }
        }
    }
});
</script>
<?php endif; ?>
