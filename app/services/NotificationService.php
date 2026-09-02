<?php
// app/services/NotificationService.php

class NotificationService {
    private static ?NotificationService $instance = null;
    private array $listeners = [];
    private $notificationModel;

    private function __construct() {
        require_once ROOT_PATH . '/app/models/Notification.php';
        $this->notificationModel = new Notification();
    }

    /**
     * Singleton : retourne l'instance unique du service
     */
    public static function getInstance(): NotificationService {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Enregistre un listener pour un canal donné
     */
    public function register(string $channel, NotificationListener $listener): void {
        $this->listeners[$channel] = $listener;
    }

    /**
     * Dispatch un événement et crée les notifications associées
     */
    public function dispatch(string $eventType, array $context): void {
        try {
            // Déterminer les destinataires et les infos de notification
            [$recipients, $notification_data] = $this->resolveNotification($eventType, $context);

            if (empty($recipients)) {
                return;
            }

            // Créer une notification pour chaque destinataire
            foreach ($recipients as $recipient_id) {
                try {
                    $notificationId = $this->createNotification(
                        $recipient_id,
                        $eventType,
                        $notification_data['title'],
                        $notification_data['body'],
                        $notification_data['entity_type'] ?? 'eleve',
                        $notification_data['entity_id'] ?? $context['eleve_id'] ?? null,
                        $context
                    );

                    // Notifier les listeners
                    $this->notifyListeners([
                        'id'                  => $notificationId,
                        'recipient_id'        => $recipient_id,
                        'event_type'          => $eventType,
                        'related_entity_type' => $notification_data['entity_type'] ?? 'eleve',
                        'related_entity_id'   => $notification_data['entity_id'] ?? $context['eleve_id'] ?? null,
                        'title'               => $notification_data['title'],
                        'body'                => $notification_data['body'],
                        'data_json'           => json_encode($context)
                    ]);
                } catch (Exception $e) {
                    error_log('SGE Notification Error (recipient ' . $recipient_id . '): ' . $e->getMessage());
                }
            }
        } catch (Exception $e) {
            error_log('SGE Notification Dispatch Error: ' . $e->getMessage());
        }
    }

    /**
     * Crée une notification en base de données
     */
    private function createNotification(
        int $recipientId,
        string $eventType,
        string $title,
        string $body,
        string $entityType,
        ?int $entityId,
        array $context
    ): int {
        return $this->notificationModel->insert([
            'recipient_id'        => $recipientId,
            'event_type'          => $eventType,
            'related_entity_type' => $entityType,
            'related_entity_id'   => $entityId ?? 0,
            'title'               => $title,
            'body'                => $body,
            'data_json'           => json_encode($context)
        ]);
    }

    /**
     * Notifie les listeners enregistrés
     */
    private function notifyListeners(array $notification): void {
        foreach ($this->listeners as $channel => $listener) {
            try {
                $success = $listener->handle($notification);
                $status = $success ? 'sent' : 'failed';
            } catch (Exception $e) {
                $success = false;
                $status = 'failed';
                error_log('Listener error (' . $channel . '): ' . $e->getMessage());
            }

            // Logger la tentative d'envoi
            $this->logNotificationAttempt($notification['id'], $channel, $status, $success ? null : 'Error');
        }
    }

    /**
     * Log une tentative d'envoi
     */
    private function logNotificationAttempt(int $notificationId, string $channel, string $status, ?string $error): void {
        try {
            $db = Database::getConnection();
            $db->prepare("
                INSERT INTO `notification_logs` (notification_id, channel, status, error_message, sent_at)
                VALUES (?, ?, ?, ?, ?)
            ")->execute([
                $notificationId,
                $channel,
                $status,
                $error,
                $status === 'sent' ? date('Y-m-d H:i:s') : null
            ]);
        } catch (Exception $e) {
            error_log('Failed to log notification attempt: ' . $e->getMessage());
        }
    }

    /**
     * Résout les destinataires et les données pour un événement
     * Retourne : [['recipient_ids'], ['notification_data']]
     */
    private function resolveNotification(string $eventType, array $context): array {
        $recipients = [];
        $notificationData = [];

        require_once ROOT_PATH . '/app/models/Eleve.php';
        require_once ROOT_PATH . '/app/models/User.php';
        require_once ROOT_PATH . '/app/models/Matiere.php';

        $eleveModel = new Eleve();
        $userModel = new User();
        $matiereModel = new Matiere();

        $eleveId = $context['eleve_id'] ?? null;
        if (!$eleveId) return [[], []];

        $eleve = $eleveModel->ficheComplete($eleveId);
        if (!$eleve) return [[], []];

        switch ($eventType) {
            case 'note.created':
            case 'note.deleted':
                // Notifier : parent de l'élève + prof + admins
                if ($eleve['parent_id']) {
                    $recipients[] = (int)$eleve['parent_id'];
                }

                $matiereId = $context['matiere_id'] ?? null;
                if ($matiereId) {
                    $matiere = $matiereModel->findById($matiereId);
                    if ($matiere && $matiere['prof_id']) {
                        $recipients[] = (int)$matiere['prof_id'];
                    }
                }

                // Ajouter tous les admins
                $admins = $userModel->findBy(['role' => ROLE_ADMIN, 'actif' => 1]);
                $recipients = array_merge($recipients, array_map(fn($a) => $a['id'], $admins ?? []));

                $matiereNom = $matiere['nom'] ?? 'Matière';
                $note = $context['note_value'] ?? 0;
                $notificationData = [
                    'title'       => 'Nouvelle note enregistrée',
                    'body'        => sprintf(
                        '%s a reçu une note de %s/20 en %s',
                        $eleve['prenom'] . ' ' . $eleve['nom'],
                        $note,
                        $matiereNom
                    ),
                    'entity_type' => 'note',
                    'entity_id'   => $context['note_id'] ?? null
                ];
                break;

            case 'absence.created':
                // Notifier : parent + prof + admins
                if ($eleve['parent_id']) {
                    $recipients[] = (int)$eleve['parent_id'];
                }

                $admins = $userModel->findBy(['role' => ROLE_ADMIN, 'actif' => 1]);
                $recipients = array_merge($recipients, array_map(fn($a) => $a['id'], $admins ?? []));

                $motif = $context['motif'] ?? 'Non justifiée';
                $notificationData = [
                    'title'       => 'Absence enregistrée',
                    'body'        => sprintf(
                        'Absence de %s le %s (%s)',
                        $eleve['prenom'] . ' ' . $eleve['nom'],
                        $context['date_absence'] ?? date('d/m/Y'),
                        $motif
                    ),
                    'entity_type' => 'absence',
                    'entity_id'   => $context['absence_id'] ?? null
                ];
                break;

            case 'absence.justified':
                // Notifier : parent + admins
                if ($eleve['parent_id']) {
                    $recipients[] = (int)$eleve['parent_id'];
                }

                $admins = $userModel->findBy(['role' => ROLE_ADMIN, 'actif' => 1]);
                $recipients = array_merge($recipients, array_map(fn($a) => $a['id'], $admins ?? []));

                $notificationData = [
                    'title'       => 'Absence justifiée',
                    'body'        => 'L\'absence de ' . $eleve['prenom'] . ' ' . $eleve['nom'] . ' a été justifiée.',
                    'entity_type' => 'absence',
                    'entity_id'   => $context['absence_id'] ?? null
                ];
                break;

            case 'absence.deleted':
                // Notifier : parent + admins (suppression)
                if ($eleve['parent_id']) {
                    $recipients[] = (int)$eleve['parent_id'];
                }

                $admins = $userModel->findBy(['role' => ROLE_ADMIN, 'actif' => 1]);
                $recipients = array_merge($recipients, array_map(fn($a) => $a['id'], $admins ?? []));

                $notificationData = [
                    'title'       => 'Absence supprimée',
                    'body'        => 'Une absence de ' . $eleve['prenom'] . ' ' . $eleve['nom'] . ' a été supprimée du dossier.',
                    'entity_type' => 'absence',
                    'entity_id'   => $context['absence_id'] ?? null
                ];
                break;

            case 'payment.created':
                // Notifier : parent + admins
                if ($eleve['parent_id']) {
                    $recipients[] = (int)$eleve['parent_id'];
                }

                $admins = $userModel->findBy(['role' => ROLE_ADMIN, 'actif' => 1]);
                $recipients = array_merge($recipients, array_map(fn($a) => $a['id'], $admins ?? []));

                $montant = $context['montant_fcfa'] ?? 0;
                $notificationData = [
                    'title'       => 'Paiement reçu',
                    'body'        => sprintf(
                        'Paiement de %s FCFA reçu pour %s',
                        number_format($montant, 0, ',', ' '),
                        $eleve['prenom'] . ' ' . $eleve['nom']
                    ),
                    'entity_type' => 'paiement',
                    'entity_id'   => $context['payment_id'] ?? null
                ];
                break;

            case 'payment.cancelled':
                // Notifier : parent + admins
                if ($eleve['parent_id']) {
                    $recipients[] = (int)$eleve['parent_id'];
                }

                $admins = $userModel->findBy(['role' => ROLE_ADMIN, 'actif' => 1]);
                $recipients = array_merge($recipients, array_map(fn($a) => $a['id'], $admins ?? []));

                $notificationData = [
                    'title'       => 'Paiement annulé',
                    'body'        => 'Un paiement pour ' . $eleve['prenom'] . ' ' . $eleve['nom'] . ' a été annulé.',
                    'entity_type' => 'paiement',
                    'entity_id'   => $context['payment_id'] ?? null
                ];
                break;
        }

        // Dédupliquer et retirer les valeurs nulles
        $recipients = array_values(array_unique(array_filter($recipients)));

        return [$recipients, $notificationData];
    }
}
