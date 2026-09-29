<?php

require_once ROOT_PATH . '/app/core/Model.php';

class Enseignant extends Model {
    protected string $table = 'enseignants';

    public function ensureForUser(int $userId, ?array $userData = null): ?array {
        $existing = $this->queryOne(
            "SELECT e.*, u.nom, u.prenom, u.email, u.actif, u.role, u.photo AS user_photo
             FROM `enseignants` e
             JOIN `users` u ON u.id = e.user_id
             WHERE e.user_id = ? LIMIT 1",
            [$userId]
        );

        if ($existing) {
            return $existing;
        }

        if ($userData !== null) {
            $user = $userData;
        } else {
            $stmt = Database::getConnection()->prepare("SELECT * FROM `users` WHERE id = ? LIMIT 1");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
        }

        if (!$user) {
            return null;
        }

        $inserted = [
            'user_id' => $userId,
            'nom' => trim((string) ($user['nom'] ?? '')),
            'prenom' => trim((string) ($user['prenom'] ?? '')),
            'email' => trim((string) ($user['email'] ?? '')),
            'photo' => $user['photo'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->prepare(
            "INSERT INTO `enseignants` (`user_id`, `nom`, `prenom`, `email`, `photo`, `created_at`) VALUES (?, ?, ?, ?, ?, ?)"
        )->execute([
            (int) $inserted['user_id'],
            $inserted['nom'],
            $inserted['prenom'],
            $inserted['email'],
            $inserted['photo'],
            $inserted['created_at'],
        ]);

        return $this->queryOne(
            "SELECT e.*, u.nom, u.prenom, u.email, u.actif, u.role, u.photo AS user_photo
             FROM `enseignants` e
             JOIN `users` u ON u.id = e.user_id
             WHERE e.user_id = ? LIMIT 1",
            [$userId]
        );
    }

    public function list(string $q = '', string $statut = ''): array {
        $where = [];
        $params = [];

        if ($q !== '') {
            $where[] = "(
                u.nom LIKE ? OR u.prenom LIKE ? OR e.matricule LIKE ? OR u.email LIKE ? OR e.telephone LIKE ? OR e.telephone_professionnel LIKE ?
            )";
            $like = '%' . $q . '%';
            $params = array_fill(0, 6, $like);
        }

        if ($statut !== '') {
            $where[] = "u.actif = ?";
            $params[] = $statut === 'actif' ? 1 : 0;
        }

        $sql = "SELECT e.*, u.nom, u.prenom, u.email, u.actif, u.role, u.created_at AS user_created_at
                FROM `enseignants` e
                JOIN `users` u ON u.id = e.user_id";

        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY u.nom, u.prenom';

        return $this->query($sql, $params);
    }

    public function byUserId(int $userId): ?array {
        return $this->queryOne(
            "SELECT e.*, u.nom, u.prenom, u.email, u.actif, u.role, u.photo AS user_photo
             FROM `enseignants` e
             JOIN `users` u ON u.id = e.user_id
             WHERE e.user_id = ? LIMIT 1",
            [$userId]
        );
    }

    public function findById(int $id): ?array {
        return $this->queryOne(
            "SELECT e.*, u.nom, u.prenom, u.email, u.actif, u.role, u.photo AS user_photo
             FROM `enseignants` e
             JOIN `users` u ON u.id = e.user_id
             WHERE e.id = ? LIMIT 1",
            [$id]
        );
    }

    public function permissionsForUser(int $userId): array {
        return $this->query(
            "SELECT p.id, p.nom, p.code, p.module,
                    COALESCE(up.effect, '') AS effect
             FROM `permissions` p
             LEFT JOIN `user_permissions` up
                ON up.permission_id = p.id AND up.user_id = ?
             WHERE p.statut = 1
             ORDER BY p.module, p.nom",
            [$userId]
        );
    }

    public function savePermissions(int $userId, array $permissions): void {
        $this->db->beginTransaction();
        try {
            $this->db->prepare("DELETE FROM `user_permissions` WHERE `user_id` = ?")->execute([$userId]);
            $stmt = $this->db->prepare(
                "INSERT INTO `user_permissions` (`user_id`, `permission_id`, `effect`, `created_by`) VALUES (?, ?, ?, ?)"
            );
            foreach ($permissions as $permissionId => $effect) {
                if (in_array($effect, ['allow', 'deny'], true)) {
                    $stmt->execute([(int) $userId, (int) $permissionId, $effect, (int) AuthMiddleware::user()['id']]);
                }
            }
            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function upsertProfile(int $userId, array $teacherData): void {
        $existing = $this->byUserId($userId);

        if ($existing) {
            $cols = [];
            $values = [];
            foreach ($teacherData as $field => $value) {
                $cols[] = "`{$field}` = ?";
                $values[] = $value;
            }
            $values[] = $userId;

            $this->db->prepare(
                "UPDATE `enseignants` SET " . implode(', ', $cols) . " WHERE `user_id` = ?"
            )->execute($values);
            return;
        }

        $fieldList = [
            'user_id', 'nom', 'prenom', 'sexe', 'date_naissance', 'lieu_naissance', 'nationalite',
            'telephone', 'email', 'adresse', 'photo', 'matricule', 'niveau_etude', 'specialite',
            'diplome', 'experience', 'date_recrutement', 'statut_professionnel', 'type_contrat',
            'telephone_professionnel', 'email_professionnel', 'created_at', 'updated_at'
        ];
        $values = [
            (int) $userId,
            $teacherData['nom'] ?? '',
            $teacherData['prenom'] ?? '',
            $teacherData['sexe'] ?? 'M',
            $teacherData['date_naissance'] ?? null,
            $teacherData['lieu_naissance'] ?? '',
            $teacherData['nationalite'] ?? '',
            $teacherData['telephone'] ?? '',
            $teacherData['email'] ?? '',
            $teacherData['adresse'] ?? '',
            $teacherData['photo'] ?? '',
            $teacherData['matricule'] ?? '',
            $teacherData['niveau_etude'] ?? '',
            $teacherData['specialite'] ?? '',
            $teacherData['diplome'] ?? '',
            $teacherData['experience'] ?? '',
            $teacherData['date_recrutement'] ?? null,
            $teacherData['statut_professionnel'] ?? '',
            $teacherData['type_contrat'] ?? '',
            $teacherData['telephone_professionnel'] ?? '',
            $teacherData['email_professionnel'] ?? '',
            $teacherData['created_at'] ?? date('Y-m-d H:i:s'),
            $teacherData['updated_at'] ?? date('Y-m-d H:i:s'),
        ];

        $placeholders = implode(', ', array_fill(0, count($fieldList), '?'));
        $this->db->prepare(
            "INSERT INTO `enseignants` (`" . implode('`, `', $fieldList) . "`) VALUES ({$placeholders})"
        )->execute($values);
    }

    public function classesForTeacher(int $teacherId): array {
        return $this->query(
            "SELECT DISTINCT c.*, n.nom AS niveau_nom,
                    CASE
                        WHEN TRIM(c.niveau) = '' THEN TRIM(c.nom)
                        ELSE TRIM(CONCAT(COALESCE(NULLIF(n.nom, ''), c.niveau), ' ', c.nom))
                    END AS libelle_complete
             FROM `teacher_assignments` ta
             JOIN `classes` c ON c.id = ta.class_id AND c.annee_scolaire_id = ta.school_year_id
             LEFT JOIN `niveaux` n ON n.id = c.niveau_id
             WHERE ta.teacher_id = ? AND ta.status = 1 AND c.statut = 1
             ORDER BY n.ordre, c.nom",
            [$teacherId]
        );
    }

    public function subjectsForTeacher(int $teacherId): array {
        return $this->query(
            "SELECT DISTINCT m.*
             FROM `teacher_assignments` ta
             JOIN `matieres` m ON m.id = ta.subject_id
             WHERE ta.teacher_id = ? AND ta.status = 1 AND m.statut = 1
             ORDER BY m.nom",
            [$teacherId]
        );
    }

    public function assignmentsForTeacher(int $teacherId): array {
        return $this->query(
            "SELECT ta.*, c.nom AS classe_nom, n.nom AS niveau_nom, m.nom AS matiere_nom, a.libelle AS annee_libelle
             FROM `teacher_assignments` ta
             JOIN `classes` c ON c.id = ta.class_id
             LEFT JOIN `niveaux` n ON n.id = c.niveau_id
             JOIN `matieres` m ON m.id = ta.subject_id
             JOIN `annees_scolaires` a ON a.id = ta.school_year_id
             WHERE ta.teacher_id = ?
             ORDER BY a.libelle DESC, n.ordre, c.nom, m.nom",
            [$teacherId]
        );
    }
}
