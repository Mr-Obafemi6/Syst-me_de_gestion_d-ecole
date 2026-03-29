<?php
// app/models/User.php

require_once ROOT_PATH . '/app/core/Model.php';

class User extends Model {
    protected string $table = 'users';

    /**
     * Trouve un utilisateur par email
     */
    public function findByEmail(string $email): ?array {
        return $this->queryOne(
            "SELECT * FROM `users` WHERE `email` = ? AND `actif` = 1 LIMIT 1",
            [trim(strtolower($email))]
        );
    }

    /**
     * Vérifie les identifiants et retourne l'utilisateur ou null
     */
    public function authenticate(string $email, string $password): ?array {
        $user = $this->findByEmail($email);
        if (!$user) return null;
        if (!password_verify($password, $user['password_hash'])) return null;
        return $user;
    }

    /**
     * Crée un utilisateur avec mot de passe haché
     */
    public function createUser(array $data): int {
        $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        $data['email']         = strtolower(trim($data['email']));
        unset($data['password']);
        return $this->insert($data);
    }

    /**
     * Change le mot de passe d'un utilisateur
     */
    public function changePassword(int $userId, string $newPassword): bool {
        return $this->update($userId, [
            'password_hash' => password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12])
        ]);
    }

    /**
     * Génère un token de réinitialisation de mot de passe
     */
    public function generateResetToken(string $email): ?string {
        $user = $this->findByEmail($email);
        if (!$user) return null;

        $token  = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', time() + 3600); // 1h

        $this->update($user['id'], [
            'reset_token'  => $token,
            'reset_expiry' => $expiry,
        ]);
        return $token;
    }

    /**
     * Valide un token de reset et retourne l'utilisateur
     */
    public function findByResetToken(string $token): ?array {
        return $this->queryOne(
            "SELECT * FROM `users`
             WHERE `reset_token` = ?
               AND `reset_expiry` > NOW()
               AND `actif` = 1
             LIMIT 1",
            [$token]
        );
    }

    /**
     * Invalide le token de reset après utilisation
     */
    public function clearResetToken(int $userId): void {
        $this->update($userId, ['reset_token' => null, 'reset_expiry' => null]);
    }

    /**
     * Retourne tous les users avec leur rôle (pour la gestion admin)
     */
    public function getAllWithRole(): array {
        return $this->query(
            "SELECT id, nom, prenom, email, role, actif, created_at
             FROM `users`
             ORDER BY nom, prenom"
        );
    }

    /**
     * Vérifie si un email existe déjà
     */
    public function emailExists(string $email, int $excludeId = 0): bool {
        $count = $this->queryScalar(
            "SELECT COUNT(*) FROM `users` WHERE `email` = ? AND `id` != ?",
            [strtolower(trim($email)), $excludeId]
        );
        return (int)$count > 0;
    }
}
