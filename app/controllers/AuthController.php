<?php
// app/controllers/AuthController.php

require_once ROOT_PATH . '/app/core/Controller.php';
require_once ROOT_PATH . '/app/models/User.php';

class AuthController extends Controller {

    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    // ─────────────────────────────────────────
    // GET  /auth/login
    // ─────────────────────────────────────────
    public function login(?string $param = null): void {
        // Déjà connecté → dashboard
        if (AuthMiddleware::isLoggedIn()) {
            Router::redirect('dashboard');
        }

        $this->render('auth/login', [
            'title'      => 'Connexion',
            'csrf_token' => $this->generateCsrfToken(),
            'expired'    => isset($_GET['expired']),
            'error'      => null,
        ], 'auth');
    }

    // ─────────────────────────────────────────
    // POST /auth/login
    // ─────────────────────────────────────────
    public function doLogin(?string $param = null): void {
        $this->requireMethod('POST');
        $this->validateCsrf();

        $email    = $this->post('email', '');
        $password = $this->post('password', '');

        // Validation basique
        if (empty($email) || empty($password)) {
            $this->renderLogin('Veuillez remplir tous les champs.');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->renderLogin('Adresse email invalide.');
            return;
        }

        // Tentative d'authentification
        $user = $this->userModel->authenticate($email, $password);

        if (!$user) {
            // Délai anti-brute force
            sleep(1);
            $this->renderLogin('Email ou mot de passe incorrect.');
            return;
        }

        // Connexion réussie
        AuthMiddleware::login($user);
        $this->flash('success', 'Bienvenue, ' . htmlspecialchars($user['prenom']) . ' !');
        Router::redirect('dashboard');
    }

    // ─────────────────────────────────────────
    // GET  /auth/logout
    // ─────────────────────────────────────────
    public function logout(?string $param = null): void {
        AuthMiddleware::logout();
        Router::redirect('auth/login');
    }

    // ─────────────────────────────────────────
    // GET  /auth/forgot
    // ─────────────────────────────────────────
    public function forgot(?string $param = null): void {
        if (AuthMiddleware::isLoggedIn()) Router::redirect('dashboard');

        $this->render('auth/forgot', [
            'title'      => 'Mot de passe oublié',
            'csrf_token' => $this->generateCsrfToken(),
            'sent'       => false,
            'error'      => null,
        ], 'auth');
    }

    // ─────────────────────────────────────────
    // POST /auth/forgot
    // ─────────────────────────────────────────
    public function doForgot(?string $param = null): void {
        $this->requireMethod('POST');
        $this->validateCsrf();

        $email = $this->post('email', '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->render('auth/forgot', [
                'title'      => 'Mot de passe oublié',
                'csrf_token' => $this->generateCsrfToken(),
                'sent'       => false,
                'error'      => 'Adresse email invalide.',
            ], 'auth');
            return;
        }

        // Générer le token (même si l'email n'existe pas — sécurité anti-énumération)
        $token = $this->userModel->generateResetToken($email);

        // En production : envoyer un email avec le lien
        // Pour l'instant : afficher le lien directement (développement)
        $resetLink = Router::url('auth/reset?token=' . $token);

        $this->render('auth/forgot', [
            'title'      => 'Mot de passe oublié',
            'csrf_token' => $this->generateCsrfToken(),
            'sent'       => true,
            'reset_link' => $resetLink, // À retirer en production
            'error'      => null,
        ], 'auth');
    }

    // ─────────────────────────────────────────
    // GET  /auth/reset?token=xxx
    // ─────────────────────────────────────────
    public function reset(?string $param = null): void {
        $token = $this->get('token', '');
        $user  = $token ? $this->userModel->findByResetToken($token) : null;

        if (!$user) {
            $this->flash('error', 'Lien invalide ou expiré.');
            Router::redirect('auth/login');
        }

        $this->render('auth/reset', [
            'title'      => 'Nouveau mot de passe',
            'csrf_token' => $this->generateCsrfToken(),
            'token'      => $token,
            'error'      => null,
        ], 'auth');
    }

    // ─────────────────────────────────────────
    // POST /auth/doReset
    // ─────────────────────────────────────────
    public function doReset(?string $param = null): void {
        $this->requireMethod('POST');
        $this->validateCsrf();

        $token    = $this->post('token', '');
        $password = $this->post('password', '');
        $confirm  = $this->post('confirm', '');

        $user = $token ? $this->userModel->findByResetToken($token) : null;

        if (!$user) {
            $this->flash('error', 'Lien invalide ou expiré.');
            Router::redirect('auth/login');
        }

        $error = $this->validatePassword($password, $confirm);
        if ($error) {
            $this->render('auth/reset', [
                'title'      => 'Nouveau mot de passe',
                'csrf_token' => $this->generateCsrfToken(),
                'token'      => $token,
                'error'      => $error,
            ], 'auth');
            return;
        }

        $this->userModel->changePassword($user['id'], $password);
        $this->userModel->clearResetToken($user['id']);

        $this->flash('success', 'Mot de passe modifié avec succès. Veuillez vous connecter.');
        Router::redirect('auth/login');
    }

    // ─────────────────────────────────────────
    // PROFIL — GET /auth/profil
    // ─────────────────────────────────────────
    public function profil(?string $param = null): void {
        AuthMiddleware::requireAuth();
        $user = AuthMiddleware::user();

        $this->render('auth/profil', [
            'title'      => 'Mon profil',
            'pageTitle'  => 'Mon profil',
            'user'       => $user,
            'csrf_token' => $this->generateCsrfToken(),
            'flash'      => $this->getFlash(),
            'error'      => null,
        ]);
    }

    // ─────────────────────────────────────────
    // PROFIL — POST /auth/updatePassword
    // ─────────────────────────────────────────
    public function updatePassword(?string $param = null): void {
        AuthMiddleware::requireAuth();
        $this->requireMethod('POST');
        $this->validateCsrf();

        $user        = AuthMiddleware::user();
        $current     = $this->post('current_password', '');
        $newPassword = $this->post('new_password', '');
        $confirm     = $this->post('confirm_password', '');

        // Vérifier l'ancien mot de passe
        $dbUser = $this->userModel->findById($user['id']);
        if (!password_verify($current, $dbUser['password_hash'])) {
            $this->render('auth/profil', [
                'title'      => 'Mon profil',
                'pageTitle'  => 'Mon profil',
                'user'       => $user,
                'csrf_token' => $this->generateCsrfToken(),
                'flash'      => null,
                'error'      => 'Mot de passe actuel incorrect.',
            ]);
            return;
        }

        $error = $this->validatePassword($newPassword, $confirm);
        if ($error) {
            $this->render('auth/profil', [
                'title'      => 'Mon profil',
                'pageTitle'  => 'Mon profil',
                'user'       => $user,
                'csrf_token' => $this->generateCsrfToken(),
                'flash'      => null,
                'error'      => $error,
            ]);
            return;
        }

        $this->userModel->changePassword($user['id'], $newPassword);
        $this->flash('success', 'Mot de passe mis à jour avec succès.');
        Router::redirect('auth/profil');
    }

    // ─────────────────────────────────────────
    // HELPERS PRIVÉS
    // ─────────────────────────────────────────

    private function renderLogin(string $error): void {
        $this->render('auth/login', [
            'title'      => 'Connexion',
            'csrf_token' => $this->generateCsrfToken(),
            'expired'    => false,
            'error'      => $error,
        ], 'auth');
    }

    private function validatePassword(string $password, string $confirm): ?string {
        if (strlen($password) < 8) {
            return 'Le mot de passe doit contenir au moins 8 caractères.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            return 'Le mot de passe doit contenir au moins une majuscule.';
        }
        if (!preg_match('/[0-9]/', $password)) {
            return 'Le mot de passe doit contenir au moins un chiffre.';
        }
        if ($password !== $confirm) {
            return 'Les mots de passe ne correspondent pas.';
        }
        return null;
    }
}
