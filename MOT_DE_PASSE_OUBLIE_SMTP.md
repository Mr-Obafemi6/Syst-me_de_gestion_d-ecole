# Mot de passe oublié — configuration SMTP

Le projet utilisait `mail()` directement. Sur XAMPP/Windows, cela ne garantit pas l'envoi réel d'un email. Le projet utilise maintenant un petit client SMTP intégré.

## Gmail

Dans `config/config.local.php`, renseigner :

```php
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'votrecompte@gmail.com');
define('MAIL_PASSWORD', 'MOT_DE_PASSE_APPLICATION');
define('MAIL_ENCRYPTION', 'tls');
define('MAIL_FROM_ADDRESS', 'votrecompte@gmail.com');
define('MAIL_FROM_NAME', 'SGE — Système de Gestion d’École');
```

Avec Gmail, utiliser un **mot de passe d'application** lorsque le compte Google le permet, et non le mot de passe habituel du compte.

## Test

1. Vérifier qu'un utilisateur actif existe avec l'adresse email saisie.
2. Ouvrir `Mot de passe oublié`.
3. Saisir l'adresse email.
4. Vérifier la boîte de réception et les spams.
5. Cliquer sur le lien reçu.
6. Choisir le nouveau mot de passe.
7. Se reconnecter avec le nouveau mot de passe.

Le token est aléatoire, stocké sous forme de hash dans `users.reset_token`, et expire après 1 heure.
