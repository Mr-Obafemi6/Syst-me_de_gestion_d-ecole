<!-- app/views/dashboard/index.php -->

<!-- Année scolaire -->
<?php if ($annee): ?>
<div class="alert alert-primary py-2 mb-4 d-flex align-items-center gap-2">
    <i class="bi bi-calendar3-fill"></i>
    <span>Année scolaire active : <strong><?= htmlspecialchars($annee['libelle']) ?></strong></span>
</div>
<?php endif; ?>

<!-- ── KPI Cards ── -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="kpi-card kpi-blue">
            <div>
                <div class="kpi-value"><?= $nbEleves ?></div>
                <div class="kpi-label">Élèves inscrits</div>
            </div>
            <i class="bi bi-people-fill kpi-icon"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card kpi-green">
            <div>
                <div class="kpi-value"><?= $nbClasses ?></div>
                <div class="kpi-label">Classes actives</div>
            </div>
            <i class="bi bi-building kpi-icon"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card kpi-orange">
            <div>
                <div class="kpi-value"><?= $nbProfs ?></div>
                <div class="kpi-label">Professeurs</div>
            </div>
            <i class="bi bi-person-video3 kpi-icon"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card kpi-red">
            <div>
                <div class="kpi-value" style="font-size:1.3rem">
                    <?= number_format($totalEncaisse, 0, ',', ' ') ?>
                </div>
                <div class="kpi-label">FCFA encaissés</div>
            </div>
            <i class="bi bi-cash-coin kpi-icon"></i>
        </div>
    </div>
</div>

<!-- ── Ligne 2 : Graphiques ── -->
<div class="row g-4 mb-4">

    <!-- Élèves par classe -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-bar-chart-fill me-1 text-primary"></i>
                Répartition des élèves par classe
            </div>
            <div class="card-body">
                <?php if (!empty($elevesParClasse)): ?>
                <canvas id="chart-classes" height="110"></canvas>
                <?php else: ?>
                <div class="text-center text-muted py-4">
                    <i class="bi bi-bar-chart fs-2"></i>
                    <p class="mt-2">Aucune donnée disponible.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Répartition par sexe + mentions -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-pie-chart-fill me-1 text-primary"></i>
                Répartition par sexe
            </div>
            <div class="card-body d-flex justify-content-center">
                <?php if (!empty($sexeData)): ?>
                <canvas id="chart-sexe" style="max-height:160px;max-width:160px"></canvas>
                <?php else: ?>
                <div class="text-center text-muted py-3">Aucune donnée.</div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($mentions)): ?>
        <div class="card">
            <div class="card-header">
                <i class="bi bi-award-fill me-1 text-primary"></i>
                Mentions — 1er trimestre
            </div>
            <div class="card-body p-2">
                <?php
                $mentionColors = [
                    'Très bien'  => 'success',
                    'Bien'       => 'primary',
                    'Assez bien' => 'info',
                    'Passable'   => 'warning',
                    'Insuffisant'=> 'danger',
                ];
                $totalElMentions = array_sum(array_column($mentions, 'total'));
                foreach ($mentions as $m):
                    $pct = $totalElMentions > 0 ? round(($m['total'] / $totalElMentions) * 100) : 0;
                    $cls = $mentionColors[$m['mention']] ?? 'secondary';
                ?>
                <div class="mb-2">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-semibold"><?= htmlspecialchars($m['mention']) ?></span>
                        <span class="small text-muted"><?= $m['total'] ?> élève<?= $m['total'] > 1 ? 's' : '' ?> (<?= $pct ?>%)</span>
                    </div>
                    <div class="progress" style="height:8px">
                        <div class="progress-bar bg-<?= $cls ?>" style="width:<?= $pct ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ── Ligne 3 : Paiements par mois + Accès rapides ── -->
<div class="row g-4 mb-4">

    <!-- Paiements par mois -->
    <?php if (!empty($paiementsParMois)): ?>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-graph-up me-1 text-primary"></i>
                Encaissements par mois (FCFA)
            </div>
            <div class="card-body">
                <canvas id="chart-paiements" height="100"></canvas>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Accès rapides -->
    <div class="col-lg-<?= !empty($paiementsParMois) ? 4 : 12 ?>">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-lightning-fill me-1 text-primary"></i>
                Accès rapides
            </div>
            <div class="card-body d-flex flex-column gap-2">
                <a href="<?= Router::url('eleves/ajouter') ?>" class="btn btn-outline-primary btn-sm text-start">
                    <i class="bi bi-person-plus me-2"></i> Ajouter un élève
                </a>
                <a href="<?= Router::url('classes/ajouter') ?>" class="btn btn-outline-success btn-sm text-start">
                    <i class="bi bi-plus-circle me-2"></i> Créer une classe
                </a>
                <a href="<?= Router::url('notes') ?>" class="btn btn-outline-warning btn-sm text-start">
                    <i class="bi bi-pencil-square me-2"></i> Saisir des notes
                </a>
                <a href="<?= Router::url('paiements/ajouter') ?>" class="btn btn-outline-danger btn-sm text-start">
                    <i class="bi bi-cash-coin me-2"></i> Enregistrer un paiement
                </a>
                <a href="<?= Router::url('bulletins') ?>" class="btn btn-outline-secondary btn-sm text-start">
                    <i class="bi bi-file-earmark-text me-2"></i> Générer des bulletins
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ── Ligne 4 : Dernières activités ── -->
<div class="row g-4">

    <!-- Derniers paiements -->
    <?php if (!empty($derniersPaiements)): ?>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-receipt me-1 text-primary"></i> Derniers paiements</span>
                <a href="<?= Router::url('paiements') ?>" class="btn btn-sm btn-outline-primary">Voir tout</a>
            </div>
            <div class="list-group list-group-flush">
                <?php foreach ($derniersPaiements as $pai): ?>
                <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                    <div>
                        <div class="small fw-semibold">
                            <?= htmlspecialchars($pai['eleve_prenom'] . ' ' . $pai['eleve_nom']) ?>
                        </div>
                        <div class="text-muted" style="font-size:.75rem">
                            <?= date('d/m/Y', strtotime($pai['date_paiement'])) ?> —
                            <?= htmlspecialchars($pai['recu_numero']) ?>
                        </div>
                    </div>
                    <span class="fw-bold text-success small">
                        <?= number_format($pai['montant_fcfa'], 0, ',', ' ') ?> FCFA
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Derniers élèves -->
    <?php if (!empty($derniersEleves)): ?>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-person-plus me-1 text-primary"></i> Dernières inscriptions</span>
                <a href="<?= Router::url('eleves') ?>" class="btn btn-sm btn-outline-primary">Voir tout</a>
            </div>
            <div class="list-group list-group-flush">
                <?php foreach ($derniersEleves as $el): ?>
                <div class="list-group-item d-flex align-items-center gap-2 py-2">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                         style="width:30px;height:30px;font-size:.7rem;font-weight:700;flex-shrink:0">
                        <?= strtoupper(substr($el['prenom'],0,1) . substr($el['nom'],0,1)) ?>
                    </div>
                    <div class="flex-fill">
                        <div class="small fw-semibold">
                            <?= htmlspecialchars($el['prenom'] . ' ' . $el['nom']) ?>
                        </div>
                        <div class="text-muted" style="font-size:.75rem">
                            <?= htmlspecialchars($el['matricule']) ?> —
                            <?= htmlspecialchars($el['classe_nom'] ?? '') ?>
                        </div>
                    </div>
                    <span class="text-muted" style="font-size:.75rem">
                        <?= date('d/m/Y', strtotime($el['created_at'])) ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<!-- ── Chart.js ── -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
// Palette
const colors = {
    blue:   'rgba(15,52,96,0.8)',
    green:  'rgba(26,122,74,0.8)',
    orange: 'rgba(183,68,10,0.8)',
    red:    'rgba(220,53,69,0.8)',
    purple: 'rgba(125,60,152,0.8)',
    teal:   'rgba(13,110,113,0.8)',
};
const colorsArr = Object.values(colors);

// ── Graphique élèves par classe ──
<?php if (!empty($elevesParClasse)): ?>
new Chart(document.getElementById('chart-classes'), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($elevesParClasse, 'classe_nom')) ?>,
        datasets: [{
            label: 'Élèves',
            data:  <?= json_encode(array_map('intval', array_column($elevesParClasse, 'total'))) ?>,
            backgroundColor: colorsArr,
            borderRadius: 5,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});
<?php endif; ?>

// ── Graphique sexe (donut) ──
<?php if (!empty($sexeData)): ?>
<?php
$sexeLabels = array_map(fn($s) => $s['sexe'] === 'M' ? 'Masculin' : 'Féminin', $sexeData);
$sexeTotaux = array_map(fn($s) => (int)$s['total'], $sexeData);
?>
new Chart(document.getElementById('chart-sexe'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($sexeLabels) ?>,
        datasets: [{
            data: <?= json_encode($sexeTotaux) ?>,
            backgroundColor: ['rgba(15,52,96,0.8)', 'rgba(125,60,152,0.8)'],
            borderWidth: 2,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom', labels: { font: { size: 11 } } }
        },
        cutout: '60%',
    }
});
<?php endif; ?>

// ── Graphique paiements par mois (ligne) ──
<?php if (!empty($paiementsParMois)): ?>
new Chart(document.getElementById('chart-paiements'), {
    type: 'line',
    data: {
        labels: <?= json_encode(array_column($paiementsParMois, 'mois_label')) ?>,
        datasets: [{
            label: 'Encaissements (FCFA)',
            data: <?= json_encode(array_map('intval', array_column($paiementsParMois, 'total'))) ?>,
            borderColor: '#0f3460',
            backgroundColor: 'rgba(15,52,96,0.08)',
            borderWidth: 2,
            pointBackgroundColor: '#0f3460',
            pointRadius: 5,
            fill: true,
            tension: 0.3,
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
                ticks: { callback: v => new Intl.NumberFormat('fr-FR').format(v) }
            }
        }
    }
});
<?php endif; ?>
</script>
