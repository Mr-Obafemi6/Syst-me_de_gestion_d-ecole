<?php
/**
 * Petit client SMTP sans dépendance externe.
 * Utilise STARTTLS + AUTH LOGIN (compatible notamment avec Gmail).
 */
class SmtpMailer
{
    private string $host;
    private int $port;
    private string $username;
    private string $password;
    private string $encryption;
    private int $timeout;

    public function __construct(
        string $host,
        int $port,
        string $username,
        string $password,
        string $encryption = 'tls',
        int $timeout = 15
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->encryption = strtolower($encryption);
        $this->timeout = $timeout;
    }

    /**
     * Envoie un email texte simple.
     */
    public function send(string $fromEmail, string $fromName, string $toEmail, string $subject, string $body): bool
    {
        if (!filter_var($fromEmail, FILTER_VALIDATE_EMAIL) || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $transport = $this->encryption === 'ssl' ? 'ssl://' . $this->host : $this->host;
        $errno = 0;
        $errstr = '';
        $socket = @fsockopen($transport, $this->port, $errno, $errstr, $this->timeout);
        if (!$socket) {
            error_log("SGE SMTP connexion impossible: {$errstr} ({$errno})");
            return false;
        }

        stream_set_timeout($socket, $this->timeout);

        try {
            $this->expect($socket, [220]);
            $this->command($socket, 'EHLO localhost', [250]);

            if ($this->encryption === 'tls') {
                $this->command($socket, 'STARTTLS', [220]);
                $cryptoOk = @stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                if ($cryptoOk !== true) {
                    throw new RuntimeException('Impossible d\'activer TLS.');
                }
                $this->command($socket, 'EHLO localhost', [250]);
            }

            if ($this->username !== '') {
                $this->command($socket, 'AUTH LOGIN', [334]);
                $this->command($socket, base64_encode($this->username), [334]);
                $this->command($socket, base64_encode($this->password), [235]);
            }

            $this->command($socket, 'MAIL FROM:<' . $fromEmail . '>', [250]);
            $this->command($socket, 'RCPT TO:<' . $toEmail . '>', [250, 251]);
            $this->command($socket, 'DATA', [354]);

            $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
            $safeFromName = mb_encode_mimeheader($fromName, 'UTF-8', 'B', "\r\n");
            $headers = [
                'Date: ' . date(DATE_RFC2822),
                'From: ' . $safeFromName . ' <' . $fromEmail . '>',
                'To: <' . $toEmail . '>',
                'Subject: ' . $encodedSubject,
                'MIME-Version: 1.0',
                'Content-Type: text/plain; charset=UTF-8',
                'Content-Transfer-Encoding: 8bit',
            ];

            // Évite qu'une ligne commençant par un point soit interprétée comme fin de DATA.
            $body = preg_replace('/^\./m', '..', str_replace(["\r\n", "\r"], "\n", $body));
            $message = implode("\r\n", $headers) . "\r\n\r\n" . str_replace("\n", "\r\n", $body) . "\r\n.";
            fwrite($socket, $message . "\r\n");
            $this->expect($socket, [250]);

            $this->command($socket, 'QUIT', [221]);
            fclose($socket);
            return true;
        } catch (Throwable $e) {
            error_log('SGE SMTP erreur: ' . $e->getMessage());
            @fwrite($socket, "QUIT\r\n");
            @fclose($socket);
            return false;
        }
    }

    private function command($socket, string $command, array $codes): void
    {
        fwrite($socket, $command . "\r\n");
        $this->expect($socket, $codes);
    }

    private function expect($socket, array $codes): void
    {
        $response = '';
        while (($line = fgets($socket, 515)) !== false) {
            $response .= $line;
            if (strlen($line) < 4 || $line[3] !== '-') {
                break;
            }
        }

        $code = (int) substr(trim($response), 0, 3);
        if (!in_array($code, $codes, true)) {
            throw new RuntimeException('Réponse SMTP inattendue: ' . trim($response));
        }
    }
}
