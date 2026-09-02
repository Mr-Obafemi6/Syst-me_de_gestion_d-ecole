<?php
// app/services/NotificationBootstrap.php

class NotificationBootstrap {

    /**
     * Initialise le service de notifications avec les listeners
     */
    public static function initialize(): NotificationService {
        $service = NotificationService::getInstance();

        // Enregistrer les listeners
        $service->register('in_app', new InAppNotificationListener());
        $service->register('email', new EmailNotificationListener());

        return $service;
    }
}
