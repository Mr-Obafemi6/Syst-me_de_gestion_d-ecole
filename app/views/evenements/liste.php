<!-- app/views/evenements/liste.php -->
<?php
$p = $pagination;
$typeConfig = [
    'reunion' => ['label' => 'Réunion',            'color' => '#3b82f6', 'icon' => 'bi-people-fill'],
    'sortie'  => ['label' => 'Sortie pédagogique',  'color' => '#16a34a', 'icon' => 'bi-signpost-2-fill'],
    'examen'  => ['label' => 'Examen',              'color' => '#ec4899', 'icon' => 'bi-pencil-fill'],
    'conseil' => ['label' => 'Conseil de classe',   'color' => '#f59e0b', 'icon' => 'bi-easel-fill'],
    'autre'   => ['label' => 'Autre',               'color' => '#6b7280', 'icon' => 'bi-calendar-event'],
];
?>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h6 class="mb-0 text-muted"><?= $p['total'] ?> événement<?= $p['total'] > 1 ? 's' : '' ?> au total</h6>
    <a href="<?= Router::url('evenements/ajouter') ?>" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i> Ajouter un événement
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($p['data'])): ?>
        <div class="empty-state py-5">
            <i class="bi bi-calendar-event fs-2 text-muted"></i>
            <p class="mt-2 text-muted">Aucun événement enregistré pour le moment.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th>Événement</th>
                    <th>Type</th>
                    <th>Classe</th>
                    <th>Date &amp; heure</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($p['data'] as $ev): $tc = $typeConfig[$ev['type']] ?? $typeConfig['autre']; ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-start gap-3">
                            <?php if (!empty($ev['photo'])): ?>
                            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($ev['photo']) ?>" alt="Photo" 
                                 class="rounded" style="width: 60px; height: 60px; object-fit: cover; flex-shrink: 0;">
                            <?php endif; ?>
                            <div>
                                <div class="fw-semibold"><?= htmlspecialchars($ev['titre']) ?></div>
                                <?php if (!empty($ev['description'])): ?>
                                <div class="text-muted small"><?= htmlspecialchars($ev['description']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge" style="background:<?= $tc['color'] ?>1a;color:<?= $tc['color'] ?>">
                            <i class="bi <?= $tc['icon'] ?> me-1"></i><?= $tc['label'] ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($ev['classe_nom'] ?? 'Toutes classes') ?></td>
                    <td><?= date('d/m/Y à H:i', strtotime($ev['date_debut'])) ?></td>
                    <td class="text-end">
                        <form method="POST" action="<?= Router::url('evenements/supprimer/' . $ev['id']) ?>"
                              onsubmit="return confirm('Supprimer cet événement ?');" class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
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
                    <a class="page-link" href="<?= Router::url('evenements?page=' . $i) ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>
