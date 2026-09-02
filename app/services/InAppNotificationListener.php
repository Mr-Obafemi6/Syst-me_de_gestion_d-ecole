<?php
// app/services/InAppNotificationListener.php

class InAppNotificationListener implements NotificationListener {

    public function handle(array $notification): bool {
        // La notification est déjà enregistrée en base de données
        // par NotificationService::createNotification()
        // Ce listener ne fait que logger le succès
        return true;
    }
}
