<?php
// app/views/notifications/index.php
?>

<?php
$flash = $flash ?? null;
$unreadCount = $unreadCount ?? 0;
$totalCount = $totalCount ?? 0;
$page = $page ?? 1;
$limit = $limit ?? 20;
$csrf_token = $csrf_token ?? '';
?>
<div class="container-fluid mt-4">
    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                        <div>
                            <h4 class="mb-1 fw-bold">Centre de notifications</h4>
                            <p class="text-muted mb-0">Consultez et gérez toutes les actions importantes du système.</p>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                <i class="bi bi-bell-fill me-1"></i><?= $unreadCount ?> non lue(s)
                            </span>
                            <span class="badge bg-secondary-subtle text-secondary px-3 py-2">
                                <i class="bi bi-list-check me-1"></i><?= $totalCount ?> au total
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <?php if ($flash): ?>
                        <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : ($flash['type'] === 'error' ? 'danger' : 'info') ?> alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($flash['message']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
                        <div class="small text-muted">Les notifications les plus récentes apparaissent en haut.</div>
                        <div class="d-flex gap-2 flex-wrap">
                            <?php if ($unreadCount > 0): ?>
                                <form method="POST" action="<?= Router::url('notifications/marquer-tout-lu') ?>" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-check2-all me-1"></i> Tout marquer comme lu
                                    </button>
                                </form>
                            <?php endif; ?>
                            <?php if ($totalCount > 0): ?>
                                <form method="POST" action="<?= Router::url('notifications/vider-lues') ?>" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash me-1"></i> Vider les lues
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (!empty($notifications)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($notifications as $notif): ?>
                                <div class="list-group-item px-0 py-3 notification-item <?= $notif['read_at'] ? '' : 'notification-unread' ?>">
                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <div class="icon-badge <?= $notif['read_at'] ? 'bg-light text-secondary' : 'bg-primary text-white' ?> rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                                                    <i class="bi bi-<?= $notif['read_at'] ? 'bell' : 'bell-fill' ?>"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">
                                                        <?= htmlspecialchars($notif['title']) ?>
                                                    </div>
                                                    <?php if (!$notif['read_at']): ?>
                                                        <span class="badge bg-success-subtle text-success">Nouvelle</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <p class="mb-2 text-muted ps-1"><?= htmlspecialchars($notif['body']) ?></p>
                                            <div class="small text-muted ps-1">
                                                <i class="bi bi-clock me-1"></i><?= date('d/m/Y à H:i', strtotime($notif['created_at'])) ?>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2 flex-wrap">
                                            <?php if (!$notif['read_at']): ?>
                                                <form method="POST" action="<?= Router::url('notifications/marquer-lue/' . $notif['id']) ?>" class="d-inline">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-primary" title="Marquer comme lue">
                                                        <i class="bi bi-check2"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            <form method="POST" action="<?= Router::url('notifications/supprimer/' . $notif['id']) ?>" class="d-inline">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if ($totalCount > $limit): ?>
                            <nav aria-label="Pagination" class="mt-4">
                                <ul class="pagination pagination-sm">
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
                        <div class="text-center py-5">
                            <i class="bi bi-bell-slash fs-1 d-block mb-3 text-muted"></i>
                            <h5 class="fw-semibold">Aucune notification</h5>
                            <p class="text-muted mb-0">Vous n’avez rien à consulter pour le moment.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h6 class="card-title mb-0 fw-bold">À propos</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <p class="text-muted mb-3">Vous recevez des notifications sur :</p>
                    <ul class="mb-0 ps-3 text-muted">
                        <li>📝 Nouvelles notes enregistrées</li>
                        <li>📋 Absences signalées</li>
                        <li>💰 Paiements reçus</li>
                        <li>✅ Modifications importantes</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 mt-3">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h6 class="card-title mb-0 fw-bold">Actions rapides</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <a href="<?= Router::url('notifications') ?>" class="btn btn-sm btn-outline-primary w-100 mb-2">
                        <i class="bi bi-arrow-clockwise me-1"></i> Actualiser la liste
                    </a>
                    <?php if ($unreadCount > 0): ?>
                        <form method="POST" action="<?= Router::url('notifications/marquer-tout-lu') ?>" class="d-grid">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="bi bi-check2-all me-1"></i> Tout marquer comme lu
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.notification-item {
    border-left: 4px solid transparent;
    border-radius: 14px;
    transition: all 0.2s ease;
    margin-bottom: .5rem;
}

.notification-item:hover {
    transform: translateY(-1px);
    background: #f8fbff;
}

.notification-unread {
    border-left-color: #0d6efd;
    background: linear-gradient(90deg, rgba(13,110,253,0.06), rgba(13,110,253,0.02));
}
</style>
