# SGE — Système de Gestion d'École
## Version 1.0.0

Application web PHP MVC complète pour la gestion d'un établissement scolaire togolais (collège & lycée).

---

## Installation rapide (XAMPP Windows)

### 1. Copier le projet
```
C:\xampp\htdocs\SGE\
```

### 2. Importer la base de données
- Ouvrir phpMyAdmin : http://localhost/phpmyadmin
- Importer `database/sge_db.sql`
- Appliquer les migrations dans l'ordre si la base existe déjà :
  - `database/migrations/001_add_notifications_tables.sql`
  - `database/migrations/002_add_evenements_table.sql`
  - `database/migrations/003_add_comptes_parents_eleves.sql`
  - `database/migrations/005_remove_syscet_module.sql`

> La connexion PDO (`config/database.php`) applique aussi certaines réparations de schéma au démarrage.

### 3. Accéder à l'application
```
http://localhost/SGE/public/
```

### Comptes par défaut
| Email | Mot de passe | Rôle |
|-------|-------------|------|
| admin@sge.tg | Admin1234! | Administrateur |
| prof@sge.tg | Admin1234! | Professeur |
| parent@sge.tg | Admin1234! | Parent |

⚠️ Changer les mots de passe après installation.

---

## Architecture

```
SGE/
├── app/
│   ├── controllers/    # 17 contrôleurs (Auth, Dashboard, Eleve, Classe, Matiere, Note, Bulletin, Paiement, Absence, Evenement, Export, Parametre, Notification, Recherche, EspaceParent, EspaceEleve, Error)
│   ├── core/           # Router, Controller, Model (classes de base)
│   ├── middleware/     # AuthMiddleware (RBAC, sessions)
│   ├── models/         # 9 modèles (User, Eleve, Classe, Matiere, Note, Paiement, Absence, Evenement, Notification)
│   ├── services/       # Notifications (in-app + email)
│   └── views/          # Templates PHP par module + layouts
├── config/
│   ├── database.php    # Connexion PDO singleton + auto-migration légère
│   └── constants.php   # Constantes globales (rôles, BASE_URL, etc.)
├── database/
│   ├── sge_db.sql      # Schéma MySQL complet + données de test
│   └── migrations/     # Migrations incrémentales
└── public/
    ├── index.php       # Front Controller + routeur
    ├── .htaccess       # Réécriture URL + sécurité
    ├── assets/         # CSS, JS
    └── uploads/        # Logos, photos
```

---

## Modules

| Module | Routes principales | Rôles |
|--------|-------------------|-------|
| Auth | `/auth/login`, `/auth/logout`, `/auth/profil` | Tous |
| Dashboard | `/dashboard` | Admin, Prof |
| Élèves | `/eleves`, `/eleves/fiche/{id}`, `/eleves/ajouter` | Admin, Prof |
| Classes | `/classes`, `/classes/detail/{id}` | Admin, Prof |
| Matières | `/matieres`, `/matieres/ajouter` | Admin, Prof |
| Notes | `/notes`, `/notes/eleve/{id}`, `/notes/saisie` | Admin, Prof (+ lecture Parent/Élève) |
| Bulletins | `/bulletins`, `/bulletins/eleve/{id}` | Admin, Prof, Parent |
| Absences | `/absences`, `/absences/eleve/{id}` | Admin, Prof (+ lecture Parent/Élève) |
| Événements | `/evenements` | Admin, Prof |
| Export | `/export`, `/export/rapports` | Admin, Prof |
| Paiements | `/paiements`, `/paiements/recu/{id}` | Admin |
| Paramètres | `/parametres` | Admin |
| Notifications | `/notifications` | Tous (connectés) |
| Espace parent | `/espace-parent` | Parent |
| Espace élève | `/espace-eleve` | Élève |
| Recherche | `/recherche` | Admin, Prof |

---

## Rôles utilisateur

| Rôle | Constante | Accès |
|------|-----------|-------|
| Administrateur | `ROLE_ADMIN` | Gestion complète (finances, paramètres, utilisateurs) |
| Professeur | `ROLE_PROF` | Scolarité, notes, bulletins, absences |
| Parent | `ROLE_PARENT` | Espace dédié : enfants, notes, bulletins, paiements |
| Élève | `ROLE_ELEVE` | Espace dédié : profil, notes, paiements |

---

## Stack technique

- PHP 8.x (MVC sans framework)
- MySQL 8 / MariaDB 10.3+
- Bootstrap 5.3 + Bootstrap Icons
- Chart.js 4.4
- JavaScript ES6+ (fetch/async)
- XAMPP (Windows)

---

## Sécurité

- Sessions sécurisées (`httponly`, timeout 1 h)
- Mots de passe hashés (bcrypt)
- Contrôle d'accès par rôle (RBAC)
- Protection CSRF sur les formulaires POST
- Requêtes SQL préparées (PDO)
