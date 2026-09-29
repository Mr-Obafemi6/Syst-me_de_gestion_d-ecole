<?php
// app/services/AuthorizationService.php

class AuthorizationService {
    private static ?self $instance = null;
    private PDO $db;

    private function __construct() {
        $this->db = Database::getConnection();
    }

    public static function getInstance(): self {
        return self::$instance ??= new self();
    }

    public function hasPermission(string $permissionCode, ?int $userId = null): bool {
        $user = AuthMiddleware::user();
        $userId = $userId ?? (int) ($user['id'] ?? 0);
        if ($userId <= 0) return false;

        $admin = $this->db->prepare(
            "SELECT 1 FROM users u JOIN roles r ON r.id = u.role_id
             WHERE u.id = ? AND u.actif = 1 AND r.code = 'admin' LIMIT 1"
        );
        $admin->execute([$userId]);
        if ($admin->fetchColumn()) return true;

        $individual = $this->db->prepare(
            "SELECT up.effect FROM user_permissions up
             JOIN permissions p ON p.id = up.permission_id AND p.code = ? AND p.statut = 1
             WHERE up.user_id = ? LIMIT 1"
        );
        $individual->execute([$permissionCode, $userId]);
        $effect = $individual->fetchColumn();
        if ($effect !== false) return $effect === 'allow';

        $role = $this->db->prepare(
            "SELECT 1 FROM users u
             JOIN roles r ON r.id = u.role_id
             JOIN role_permissions rp ON rp.role_id = r.id
             JOIN permissions p ON p.id = rp.permission_id AND p.code = ? AND p.statut = 1
             WHERE u.id = ? AND u.actif = 1 LIMIT 1"
        );
        $role->execute([$permissionCode, $userId]);
        return (bool) $role->fetchColumn();
    }

    public function requirePermission(string $permissionCode): void {
        AuthMiddleware::requireAuth();
        if (!$this->hasPermission($permissionCode)) {
            http_response_code(403);
            require ROOT_PATH . '/app/views/errors/403.php';
            exit;
        }
    }

    public function teacherIsAssignedToClass(int $teacherId, int $classId): bool {
        $stmt = $this->db->prepare(
            "SELECT 1 FROM teacher_assignments ta
             JOIN classes c ON c.id = ta.class_id AND c.annee_scolaire_id = ta.school_year_id
             WHERE ta.teacher_id = ? AND ta.class_id = ? AND ta.status = 1 AND c.statut = 1
             LIMIT 1"
        );
        $stmt->execute([$teacherId, $classId]);
        return (bool) $stmt->fetchColumn();
    }

    public function teacherIsAssignedToClassAndSubject(int $teacherId, int $classId, int $subjectId): bool {
        $stmt = $this->db->prepare(
            "SELECT 1 FROM teacher_assignments ta
             JOIN classes c ON c.id = ta.class_id AND c.annee_scolaire_id = ta.school_year_id
             JOIN classe_matieres cm ON cm.classe_id = ta.class_id
                    AND cm.matiere_id = ta.subject_id
                    AND cm.annee_scolaire_id = ta.school_year_id
                    AND cm.statut = 1
             WHERE ta.teacher_id = ? AND ta.class_id = ? AND ta.subject_id = ?
               AND ta.status = 1 AND c.statut = 1
             LIMIT 1"
        );
        $stmt->execute([$teacherId, $classId, $subjectId]);
        return (bool) $stmt->fetchColumn();
    }

    public function requireTeacherClass(int $classId): void {
        $user = AuthMiddleware::user();
        if (!$user || !$this->teacherIsAssignedToClass((int) $user['id'], $classId)) {
            http_response_code(403);
            require ROOT_PATH . '/app/views/errors/403.php';
            exit;
        }
    }

    public function requireTeacherClassSubject(int $classId, int $subjectId): void {
        $user = AuthMiddleware::user();
        if (!$user || !$this->teacherIsAssignedToClassAndSubject((int) $user['id'], $classId, $subjectId)) {
            http_response_code(403);
            require ROOT_PATH . '/app/views/errors/403.php';
            exit;
        }
    }
}
