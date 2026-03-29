# SGE — Système de Gestion d'École
## Version 1.0.0 — Université de Lomé

Application web PHP MVC complète pour la gestion d'un établissement scolaire togolais.

---

## Installation rapide (XAMPP Windows)

### 1. Copier le projet
```
C:\xampp\htdocs\SGE\
```

### 2. Importer la base de données
- Ouvrir phpMyAdmin : http://localhost/phpmyadmin
- Importer `database/sge_db.sql`

### 3. Accéder à l'application
```
http://localhost/SGE/public/
```

### Comptes par défaut
| Email | Mot de passe | Rôle |
|-------|-------------|------|
| admin@sge.tg | Admin1234! | Administrateur |
| prof@sge.tg  | Admin1234! | Professeur |
| parent@sge.tg | Admin1234! | Parent |

⚠️ Changer les mots de passe après installation.

---

## Architecture

```
SGE/
├── app/
│   ├── controllers/    # 9 controllers (Auth, Dashboard, Eleve, Classe, Note, Bulletin, Paiement, Parametre, Error)
│   ├── core/           # Router, Controller, Model (classes de base)
│   ├── middleware/     # AuthMiddleware (RBAC, sessions)
│   ├── models/         # 6 modèles (User, Eleve, Classe, Matiere, Note, Paiement)
│   └── views/          # Templates PHP par module + layouts
├── config/
│   ├── database.php    # Connexion PDO singleton
│   └── constants.php   # Constantes globales
├── database/
│   └── sge_db.sql      # Schéma MySQL complet + données de test
└── public/
    ├── index.php       # Front Controller + routeur
    ├── .htaccess       # Réécriture URL + sécurité
    └── assets/         # CSS, JS
```

## Modules

| Module | Routes | Rôles |
|--------|--------|-------|
| Auth | /auth/login, /auth/logout, /auth/profil | Tous |
| Dashboard | /dashboard | Admin, Prof |
| Élèves | /eleves, /eleves/fiche/{id}, /eleves/ajouter | Admin, Prof |
| Classes | /classes, /classes/detail/{id} | Admin, Prof |
| Notes | /notes, /notes/eleve/{id} | Admin, Prof, Parent |
| Bulletins | /bulletins, /bulletins/eleve/{id} | Admin, Prof, Parent |
| Paiements | /paiements, /paiements/recu/{id} | Admin |
| Paramètres | /parametres | Admin |

## Stack technique
- PHP 8.x (MVC sans framework)
- MySQL 8 / MariaDB 10.3+
- Bootstrap 5.3 + Bootstrap Icons
- Chart.js 4.4
- JavaScript ES6+ (fetch/async)
- XAMPP (Windows)
