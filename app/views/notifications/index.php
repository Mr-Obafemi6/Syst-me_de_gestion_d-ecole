<?php
// app/views/notifications/index.php
?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-8">
            <h1><?= htmlspecialchars($pageTitle) ?></h1>

            <?php if ($flash): ?>
                <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Barre d'actions -->
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge bg-primary"><?= $unreadCount ?> non lue(s)</span>
                    <span class="text-muted">/ <?= $totalCount ?> notification(s)</span>
                </div>
                <?php if ($unreadCount > 0): ?>
                    <form method="POST" action="<?= Router::url('notifications/marquer-tout-lu') ?>" style="display:inline;">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                        <button type="submit" class="btn btn-sm btn-outline-primary">Tout marquer comme lu</button>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Liste des notifications -->
            <?php if (!empty($notifications)): ?>
                <div class="list-group">
                    <?php foreach ($notifications as $notif): ?>
                        <div class="list-group-item <?= $notif['read_at'] ? '' : 'list-group-item-light' ?>">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <?= htmlspecialchars($notif['title']) ?>
                                        <?php if (!$notif['read_at']): ?>
                                            <span class="badge bg-success">Nouvelle</span>
                                        <?php endif; ?>
                                    </h6>
                                    <p class="mb-1"><?= htmlspecialchars($notif['body']) ?></p>
                                    <small class="text-muted">
                                        <?= date('d/m/Y à H:i', strtotime($notif['created_at'])) ?>
                                    </small>
                                </div>
                                <?php if (!$notif['read_at']): ?>
                                    <form method="POST" action="<?= Router::url('notifications/marquer-lue/' . $notif['id']) ?>" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-secondary" title="Marquer comme lue">
                                            <i class="bi bi-check"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalCount > $limit): ?>
                    <nav aria-label="Pagination" class="mt-4">
                        <ul class="pagination">
                            <?php
                            $totalPages = ceil($totalCount / $limit);
                            for ($p = 1; $p <= $totalPages; $p++):
                                $active = $p === $page ? 'active' : '';
                            ?>
                                <li class="page-item <?= $active ?>">
                                    <a class="page-link" href="<?= Router::url('notifications?page=' . $p) ?>">
                                        <?= $p ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-info text-center py-5">
                    <i class="bi bi-bell-slash fs-1 d-block mb-2"></i>
                    <p>Aucune notification pour le moment.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar infos -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">À propos</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">
                        Vous recevez des notifications sur :
                    </p>
                    <ul class="mt-3 mb-0">
                        <li>📝 Nouvelles notes enregistrées</li>
                        <li>📋 Absences signalées</li>
                        <li>💰 Paiements reçus</li>
                        <li>✅ Modifications importantes</li>
                    </ul>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">Filtres</h6>
                </div>
                <div class="card-body">
                    <a href="<?= Router::url('notifications') ?>" class="btn btn-sm btn-outline-primary w-100 mb-2">
                        Toutes les notifications
                    </a>
                    <button class="btn btn-sm btn-outline-secondary w-100" disabled>
                        Non lues seulement (bientôt)
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.list-group-item {
    border-left: 4px solid transparent;
    transition: all 0.2s;
}

.list-group-item-light {
    border-left-color: #007bff;
    background-color: #f0f7ff !important;
}

.list-group-item:hover {
    transform: translateX(2px);
}
</style>
