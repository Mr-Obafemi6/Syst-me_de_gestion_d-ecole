<?php
// app/services/NotificationListener.php

interface NotificationListener {
    /**
     * Traite une notification selon son canal (email, SMS, in-app)
     * @return bool true si succès, false si erreur
     */
    public function handle(array $notification): bool;
}
