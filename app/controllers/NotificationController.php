<?php
// app/controllers/NotificationController.php

require_once ROOT_PATH . '/app/core/Controller.php';
require_once ROOT_PATH . '/app/models/Notification.php';

class NotificationController extends Controller {

    private Notification $notificationModel;

    public function __construct() {
        $this->notificationModel = new Notification();
    }

    /**
     * GET /notifications — Liste des notifications
     */
    public function index(?string $param = null): void {
        AuthMiddleware::requireAuth();

        $user = AuthMiddleware::user();
        $page = max(1, (int) $this->get('page', 1));
        $filter = $this->get('filter', ''); // all, unread

        // Récupérer les notifications
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $notifications = $this->notificationModel->forUser($user['id'], $limit, $offset);
        $unreadCount = $this->notificationModel->getUnreadCount($user['id']);
        $totalCount = count($notifications) > 0 ? count($this->notificationModel->forUser($user['id'], 999999)) : 0;

        $this->render('notifications/index', [
            'title'        => 'Mes notifications',
            'pageTitle'    => 'Centre de notifications',
            'user'         => $user,
            'flash'        => $this->getFlash(),
            'notifications' => $notifications,
            'unreadCount'  => $unreadCount,
            'totalCount'   => $totalCount,
            'page'         => $page,
            'limit'        => $limit,
            'csrf_token'   => $this->generateCsrfToken(),
        ]);
    }

    /**
     * POST /notifications/marquer-lue/{id}
     */
    public function marquerLue(?string $param = null): void {
        AuthMiddleware::requireAuth();
        $this->requireMethod('POST');
        $this->validateCsrf();

        $notificationId = (int) $param;
        $this->notificationModel->markAsRead($notificationId);

        $this->json(['success' => true, 'message' => 'Notification marquée comme lue.']);
    }

    /**
     * POST /notifications/marquer-tout-lu
     */
    public function marquerToutLu(?string $param = null): void {
        AuthMiddleware::requireAuth();
        $this->requireMethod('POST');
        $this->validateCsrf();

        $user = AuthMiddleware::user();
        $this->notificationModel->markAllAsRead($user['id']);

        $this->flash('success', 'Toutes les notifications sont marquées comme lues.');
        Router::redirect('notifications');
    }

    /**
     * Retourne les X dernières notifications non-lues (pour affichage dans le header)
     */
    public function getUnreadBadge(?string $param = null): void {
        AuthMiddleware::requireAuth();

        $user = AuthMiddleware::user();
        $unreadCount = $this->notificationModel->getUnreadCount($user['id']);

        $this->json([
            'unread_count' => $unreadCount,
            'recent' => $this->notificationModel->getRecentUnread($user['id'], 5)
        ]);
    }
}
