<?php
require_once ROOT_PATH . '/app/core/Controller.php';

class PermissionController extends Controller {
    public function index(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);
        AuthorizationService::getInstance()->requirePermission('permissions.view');
        $db = Database::getConnection();
        $roles = $db->query("SELECT * FROM roles WHERE statut=1 ORDER BY nom")->fetchAll();
        $roleId = (int) $this->get('role', $roles[0]['id'] ?? 0);
        $permissions = $db->query("SELECT * FROM permissions WHERE statut=1 ORDER BY module, nom")->fetchAll();
        $users = $db->query("SELECT id, nom, prenom, email FROM users WHERE actif=1 ORDER BY nom, prenom")->fetchAll();
        $userId = (int) $this->get('user', 0);
        $userSelected = [];
        if ($userId > 0) {
            $stmt = $db->prepare("SELECT permission_id, effect FROM user_permissions WHERE user_id=?");
            $stmt->execute([$userId]);
            foreach ($stmt->fetchAll() as $row) $userSelected[(int) $row['permission_id']] = $row['effect'];
        }
        $selected = [];
        if ($roleId > 0) {
            $stmt = $db->prepare("SELECT permission_id FROM role_permissions WHERE role_id=?");
            $stmt->execute([$roleId]);
            $selected = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
        }
        $this->render('permissions/index', [
            'title' => 'Rôles et permissions', 'pageTitle' => 'Rôles et permissions',
            'user' => AuthMiddleware::user(), 'flash' => $this->getFlash(),
            'csrf_token' => $this->generateCsrfToken(), 'roles' => $roles,
            'permissions' => $permissions, 'roleId' => $roleId, 'selected' => $selected,
            'users' => $users, 'userId' => $userId, 'userSelected' => $userSelected,
        ]);
    }

    public function save(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);
        AuthorizationService::getInstance()->requirePermission('permissions.assign');
        $this->requireMethod('POST');
        $this->validateCsrf();
        $roleId = (int) $this->post('role_id', 0);
        $permissionIds = array_map('intval', (array) ($_POST['permissions'] ?? []));
        $db = Database::getConnection();
        $db->beginTransaction();
        try {
            $stmt = $db->prepare("DELETE FROM role_permissions WHERE role_id=?");
            $stmt->execute([$roleId]);
            $stmt = $db->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?,?)");
            foreach ($permissionIds as $permissionId) $stmt->execute([$roleId, $permissionId]);
            $db->commit();
            $this->flash('success', 'Permissions enregistrées.');
        } catch (Throwable $e) {
            $db->rollBack();
            $this->flash('error', 'Impossible d’enregistrer les permissions.');
        }
        Router::redirect('permissions?role=' . $roleId);
    }

    public function saveUser(?string $param = null): void {
        AuthMiddleware::requireRole(ROLE_ADMIN);
        AuthorizationService::getInstance()->requirePermission('permissions.assign');
        $this->requireMethod('POST');
        $this->validateCsrf();
        $userId = (int) $this->post('user_id', 0);
        $db = Database::getConnection();
        $db->beginTransaction();
        try {
            $db->prepare("DELETE FROM user_permissions WHERE user_id=?")->execute([$userId]);
            $stmt = $db->prepare("INSERT INTO user_permissions (user_id, permission_id, effect, created_by) VALUES (?,?,?,?)");
            foreach ((array) ($_POST['user_permissions'] ?? []) as $permissionId => $effect) {
                if (in_array($effect, ['allow', 'deny'], true)) $stmt->execute([$userId, (int) $permissionId, $effect, (int) AuthMiddleware::user()['id']]);
            }
            $db->commit();
            $this->flash('success', 'Permissions individuelles enregistrées.');
        } catch (Throwable $e) {
            $db->rollBack();
            $this->flash('error', 'Impossible d’enregistrer les permissions individuelles.');
        }
        Router::redirect('permissions?user=' . $userId);
    }
}
