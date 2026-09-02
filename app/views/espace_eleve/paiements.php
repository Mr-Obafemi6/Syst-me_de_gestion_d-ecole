<!-- app/views/espace_eleve/paiements.php -->
<div class="mb-3">
    <a href="<?= Router::url('espace-eleve') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Retour à mon espace
    </a>
</div>

<div class="card mb-4">
    <div class="card-body d-flex align-items-center gap-3">
        <div>
            <div class="text-muted small">Total payé <?= $annee ? '(' . htmlspecialchars($annee['libelle']) . ')' : '' ?></div>
            <div class="fw-bold fs-4 text-success"><?= number_format($totalPaye, 0, ',', ' ') ?> FCFA</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-receipt me-1 text-primary"></i> Historique des paiements
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>N° reçu</th>
                    <th>Mode</th>
                    <th>Montant</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($paiements)): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">Aucun paiement enregistré.</td></tr>
                <?php else: foreach ($paiements as $p): ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($p['date_paiement'])) ?></td>
                    <td class="font-monospace small"><?= htmlspecialchars($p['recu_numero'] ?? '—') ?></td>
                    <td><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $p['mode_paiement'] ?? '—'))) ?></td>
                    <td class="fw-semibold"><?= number_format($p['montant_fcfa'], 0, ',', ' ') ?> FCFA</td>
                    <td>
                        <?php
                        $statut = $p['statut'] ?? '';
                        $badge = $statut === 'paye' ? 'success' : ($statut === 'annule' ? 'danger' : 'warning');
                        ?>
                        <span class="badge bg-<?= $badge ?>"><?= htmlspecialchars(ucfirst($statut)) ?></span>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
