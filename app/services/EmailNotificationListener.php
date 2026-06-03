<?php
// app/services/EmailNotificationListener.php

class EmailNotificationListener implements NotificationListener {

    public function handle(array $notification): bool {
        try {
            $recipient = $this->getRecipientEmail($notification['recipient_id']);
            if (!$recipient) {
                return false;
            }

            $subject = $notification['title'];
            $htmlBody = $this->buildEmailHtml($notification);

            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8\r\n";
            $headers .= "From: " . APP_NAME . " <noreply@sge.tg>\r\n";

            $result = @mail($recipient, $subject, $htmlBody, $headers);

            if (!$result) {
                error_log('Email send failed for ' . $recipient);
                return false;
            }

            return true;
        } catch (Exception $e) {
            error_log('Email Listener Error: ' . $e->getMessage());
            return false;
        }
    }

    private function getRecipientEmail(int $userId): ?string {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT email FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetchColumn();
            return $user ?: null;
        } catch (Exception $e) {
            error_log('Failed to get recipient email: ' . $e->getMessage());
            return null;
        }
    }

    private function buildEmailHtml(array $notification): string {
        $title = htmlspecialchars($notification['title']);
        $body = htmlspecialchars($notification['body']);
        $date = date('d/m/Y à H:i');

        return "
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #007bff; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                .content { background-color: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-radius: 0 0 5px 5px; }
                .footer { text-align: center; padding: 10px; font-size: 12px; color: #666; }
                .btn { display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-top: 10px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>$title</h2>
                </div>
                <div class='content'>
                    <p>$body</p>
                    <p style='color: #999; font-size: 12px;'>
                        Notification générée le $date
                    </p>
                    <a href='" . BASE_URL . "' class='btn'>Consulter votre espace</a>
                </div>
                <div class='footer'>
                    <p>" . APP_NAME . " - Système de Gestion d'École</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
}
