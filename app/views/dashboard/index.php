<!-- app/views/dashboard/index.php — v2 -->
<?php
$annee = $annee ?? null;
$nbEleves = $nbEleves ?? 0;
$nbClasses = $nbClasses ?? 0;
$nbProfs = $nbProfs ?? 0;
$totalEncaisse = $totalEncaisse ?? 0;
$presenceMoyenne = $presenceMoyenne ?? 0;
$presenceHebdo = $presenceHebdo ?? [];
$elevesParClasse = $elevesParClasse ?? [];
$paiementsParMois = $paiementsParMois ?? [];
$prochainsEvenements = $prochainsEvenements ?? [];
$topEleves = $topEleves ?? [];
$inscriptionsParMois = $inscriptionsParMois ?? [];
$sexeData = $sexeData ?? [];
$mentions = $mentions ?? [];
$derniersPaiements = $derniersPaiements ?? [];
$derniersEleves = $derniersEleves ?? [];
?>

<!-- Année scolaire -->
<?php if ($annee): ?>
<div class="annee-banner mb-4">
    <i class="bi bi-calendar3-fill"></i>
    <span>Année scolaire active : <strong><?= htmlspecialchars($annee['libelle']) ?></strong></span>
    <span class="ms-auto text-muted small d-none d-md-inline"><?= date('l d F Y') ?></span>
</div>
<?php endif; ?>

<!-- ── KPI Cards ── -->
<div class="row g-4 mb-4">
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Élèves inscrits</span>
                <span class="kpi-icon-badge kpi-badge-blue"><i class="bi bi-people-fill"></i></span>
            </div>
            <div class="kpi-value counter" data-target="<?= $nbEleves ?>"><?= $nbEleves ?></div>
            <div class="kpi-trend up"><i class="bi bi-arrow-up-short"></i> actifs</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Classes actives</span>
                <span class="kpi-icon-badge kpi-badge-purple"><i class="bi bi-building"></i></span>
            </div>
            <div class="kpi-value"><?= $nbClasses ?></div>
            <div class="kpi-trend"><i class="bi bi-dot"></i> en cours</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Professeurs</span>
                <span class="kpi-icon-badge kpi-badge-pink"><i class="bi bi-person-video3"></i></span>
            </div>
            <div class="kpi-value"><?= $nbProfs ?></div>
            <div class="kpi-trend up"><i class="bi bi-person-check"></i> affectés</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">FCFA encaissés</span>
                <span class="kpi-icon-badge kpi-badge-teal"><i class="bi bi-cash-coin"></i></span>
            </div>
            <div class="kpi-value kpi-fcfa" style="font-size:24px">
                <?= number_format($totalEncaisse, 0, ',', ' ') ?>
            </div>
            <div class="kpi-trend up"><i class="bi bi-graph-up-arrow"></i> cumulé</div>
        </div>
    </div>
</div>

<!-- ── Présence hebdomadaire + Événements à venir ── -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <span><i class="bi bi-clipboard2-check-fill me-2 text-primary"></i>Présence hebdomadaire</span>
                    <div class="text-muted" style="font-size:11.5px;margin-top:2px;margin-left:22px">Cette semaine</div>
                </div>
                <span class="badge" style="background:var(--teal-light);color:var(--teal);font-size:12px;padding:5px 12px">
                    <?= $presenceMoyenne ?>%
                </span>
            </div>
            <div class="card-body">
                <div class="presence-week">
                    <?php foreach ($presenceHebdo as $j): ?>
                    <div class="presence-day">
                        <span class="presence-pct"><?= $j['futur'] ? '—' : $j['pct'] . '%' ?></span>
                        <div class="presence-bar-wrap">
                            <div class="presence-bar<?= $j['futur'] ? ' futur' : '' ?>" style="height:<?= $j['futur'] ? 0 : $j['pct'] ?>%"></div>
                        </div>
                        <span class="presence-label"><?= $j['label'] ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar-event-fill me-2 text-primary"></i>Événements à venir</span>
                <a href="<?= Router::url('evenements') ?>" class="btn btn-sm btn-outline-primary">Calendrier</a>
            </div>
            <div class="card-body p-3">
                <?php if (empty($prochainsEvenements)): ?>
                <div class="empty-state py-4">
                    <i class="bi bi-calendar-event fs-3 text-muted"></i>
                    <p class="mt-2 text-muted small mb-2">Aucun événement à venir.</p>
                    <a href="<?= Router::url('evenements/ajouter') ?>" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Ajouter un événement
                    </a>
                </div>
                <?php else: ?>
                <div class="event-list">
                    <?php
                    $evColors = ['reunion' => '#3b82f6', 'sortie' => '#16a34a', 'examen' => '#ec4899', 'conseil' => '#f59e0b', 'autre' => '#6b7280'];
                    foreach ($prochainsEvenements as $ev):
                        $dot = $evColors[$ev['type']] ?? '#6b7280';
                    ?>
                    <div class="event-item">
                        <span class="event-dot" style="background:<?= $dot ?>"></span>
                        <div>
                            <div class="event-title">
                                <?= htmlspecialchars($ev['titre']) ?><?= !empty($ev['classe_nom']) ? ' — ' . htmlspecialchars($ev['classe_nom']) : '' ?>
                            </div>
                            <div class="event-date"><?= date('D d M · H:i', strtotime($ev['date_debut'])) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ── Ligne 2 : Graphiques ── -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-bar-chart-fill me-2 text-primary"></i>Élèves par classe</span>
                <span class="badge bg-primary-soft text-primary"><?= $nbClasses ?> classes</span>
            </div>
            <div class="card-body">
                <?php if (!empty($elevesParClasse)): ?>
                <canvas id="chart-classes" height="110"></canvas>
                <?php else: ?>
                <div class="empty-state py-4">
                    <i class="bi bi-bar-chart fs-2 text-muted"></i>
                    <p class="mt-2 text-muted">Aucune donnée disponible.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-pie-chart-fill me-2 text-primary"></i>Répartition par sexe
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
                <i class="bi bi-award-fill me-2 text-primary"></i>Mentions — trimestre
            </div>
            <div class="card-body p-3">
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
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-semibold"><?= htmlspecialchars($m['mention']) ?></span>
                        <span class="small text-muted"><?= $m['total'] ?> élève<?= $m['total'] > 1 ? 's' : '' ?> · <strong><?= $pct ?>%</strong></span>
                    </div>
                    <div class="progress" style="height:6px;border-radius:4px">
                        <div class="progress-bar bg-<?= $cls ?>" style="width:<?= $pct ?>%;transition:width .8s ease"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ── Ligne 2bis : Top élèves + Évolution des inscriptions ── -->
<div class="row g-4 mb-4">
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-trophy-fill me-2 text-primary"></i>Top 5 élèves</span>
                <span class="badge bg-primary-soft text-primary">Trimestre en cours</span>
            </div>
            <div class="card-body p-3">
                <?php if (empty($topEleves)): ?>
                <div class="empty-state py-4">
                    <i class="bi bi-trophy fs-3 text-muted"></i>
                    <p class="mt-2 text-muted small">Aucune note saisie pour ce trimestre.</p>
                </div>
                <?php else: ?>
                <div class="top-list">
                    <?php $ranks = ['🥇','🥈','🥉','4','5']; foreach ($topEleves as $i => $el): ?>
                    <div class="top-item">
                        <span class="top-rank"><?= $ranks[$i] ?? ($i+1) ?></span>
                        <div class="flex-fill">
                            <div class="top-name"><?= htmlspecialchars($el['prenom'] . ' ' . $el['nom']) ?></div>
                            <div class="top-sub"><?= htmlspecialchars($el['classe_nom'] ?? '—') ?></div>
                        </div>
                        <span class="top-avg"><?= number_format($el['moyenne'], 2) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-graph-up-arrow me-2 text-primary"></i>Évolution des inscriptions
            </div>
            <div class="card-body">
                <?php if (!empty($inscriptionsParMois)): ?>
                <canvas id="chart-inscriptions" height="110"></canvas>
                <?php else: ?>
                <div class="empty-state py-4">
                    <i class="bi bi-graph-up fs-2 text-muted"></i>
                    <p class="mt-2 text-muted">Aucune donnée disponible.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ── Ligne 3 : Encaissements ── -->
<div class="row g-4 mb-4">
    <?php if (!empty($paiementsParMois)): ?>
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-graph-up me-2 text-primary"></i>Encaissements par mois</span>
                <a href="<?= Router::url('paiements') ?>" class="btn btn-sm btn-outline-primary">Voir tout</a>
            </div>
            <div class="card-body">
                <canvas id="chart-paiements" height="100"></canvas>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
const C = {
    blue:   '#2e6dbf',
    green:  '#16a34a',
    orange: '#ea580c',
    teal:   '#0891b2',
    purple: '#7c3aed',
    red:    '#dc2626',
};
const palette = Object.values(C);

<?php if (!empty($elevesParClasse)): ?>
new Chart(document.getElementById('chart-classes'), {
    type: 'line',
    data: {
        labels: <?= json_encode(array_column($elevesParClasse, 'classe_nom')) ?>,
        datasets: [{
            label: 'Élèves',
            data: <?= json_encode(array_map('intval', array_column($elevesParClasse, 'total'))) ?>,
            borderColor: C.blue,
            backgroundColor: 'rgba(46,109,191,0.12)',
            borderWidth: 3,
            pointBackgroundColor: C.blue,
            pointBorderColor: '#fff',
            pointRadius: 5,
            pointHoverRadius: 7,
            fill: true,
            tension: 0.35,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        elements: { line: { cubicInterpolationMode: 'monotone' } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: 'rgba(0,0,0,0.04)' } },
            x: { grid: { display: false } }
        }
    }
});
<?php endif; ?>

<?php if (!empty($sexeData)): ?>
<?php
$sexeLabels = array_map(fn($s) => $s['sexe'] === 'M' ? 'Masculin' : 'Féminin', $sexeData);
$sexeTotaux = array_map(fn($s) => (int)$s['total'], $sexeData);
?>
new Chart(document.getElementById('chart-sexe'), {
    type: 'line',
    data: {
        labels: <?= json_encode($sexeLabels) ?>,
        datasets: [{
            label: 'Répartition',
            data: <?= json_encode($sexeTotaux) ?>,
            borderColor: C.purple,
            backgroundColor: 'rgba(124,58,237,0.12)',
            borderWidth: 3,
            pointBackgroundColor: C.purple,
            pointBorderColor: '#fff',
            pointRadius: 6,
            pointHoverRadius: 8,
            fill: true,
            tension: 0.35,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        elements: { line: { cubicInterpolationMode: 'monotone' } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: 'rgba(0,0,0,0.04)' } },
            x: { grid: { display: false } }
        }
    }
});
<?php endif; ?>

<?php if (!empty($inscriptionsParMois)): ?>
new Chart(document.getElementById('chart-inscriptions'), {
    type: 'line',
    data: {
        labels: <?= json_encode(array_column($inscriptionsParMois, 'mois_label')) ?>,
        datasets: [{
            label: 'Nouvelles inscriptions',
            data: <?= json_encode(array_map('intval', array_column($inscriptionsParMois, 'total'))) ?>,
            borderColor: C.teal,
            backgroundColor: 'rgba(8,145,178,0.08)',
            borderWidth: 2.5,
            pointBackgroundColor: C.teal,
            pointRadius: 4,
            pointHoverRadius: 7,
            fill: true,
            tension: 0.4,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ctx.raw + ' élève' + (ctx.raw > 1 ? 's' : '') + ' inscrit' + (ctx.raw > 1 ? 's' : '') } }
        },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: 'rgba(0,0,0,0.04)' } },
            x: { grid: { display: false } }
        }
    }
});
<?php endif; ?>

<?php if (!empty($paiementsParMois)): ?>
new Chart(document.getElementById('chart-paiements'), {
    type: 'line',
    data: {
        labels: <?= json_encode(array_column($paiementsParMois, 'mois_label')) ?>,
        datasets: [{
            label: 'FCFA',
            data: <?= json_encode(array_map('intval', array_column($paiementsParMois, 'total'))) ?>,
            borderColor: C.blue,
            backgroundColor: 'rgba(46,109,191,0.07)',
            borderWidth: 2.5,
            pointBackgroundColor: C.blue,
            pointRadius: 4,
            pointHoverRadius: 7,
            fill: true,
            tension: 0.4,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => new Intl.NumberFormat('fr-FR').format(ctx.raw) + ' FCFA' } }
        },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => new Intl.NumberFormat('fr-FR',{notation:'compact'}).format(v) }, grid: { color: 'rgba(0,0,0,0.04)' } },
            x: { grid: { display: false } }
        }
    }
});
<?php endif; ?>
</script>
