<?php
$users = $users ?? [];
$userId = (int) ($userId ?? 0);
$userSelected = $userSelected ?? [];
$roles = $roles ?? [];
$roleId = (int) ($roleId ?? 0);
$permissions = $permissions ?? [];
$selected = $selected ?? [];
$csrf_token = (string) ($csrf_token ?? '');
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h4 class="fw-bold text-primary mb-1">Rôles et permissions</h4><div class="text-muted">Configurez les accès par rôle.</div></div>
</div>
<div class="card mb-4"><div class="card-header">Permissions individuelles</div><div class="card-body"><form method="GET" action="<?= Router::url('permissions') ?>" class="row g-2 align-items-end"><div class="col-md-5"><label class="form-label">Utilisateur</label><select name="user" class="form-select"><option value="">Choisir</option><?php foreach ($users as $account): ?><option value="<?= (int) $account['id'] ?>" <?= (int) $account['id'] === $userId ? 'selected' : '' ?>><?= htmlspecialchars($account['prenom'].' '.$account['nom'].' - '.$account['email']) ?></option><?php endforeach; ?></select></div><div class="col-md-2"><button class="btn btn-outline-primary">Charger</button></div></form><?php if ($userId > 0): ?><form method="POST" action="<?= Router::url('permissions/saveUser') ?>" class="mt-3"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>"><input type="hidden" name="user_id" value="<?= $userId ?>"><div class="row"><?php foreach ($permissions as $permission): ?><div class="col-md-6 mb-2"><select name="user_permissions[<?= (int) $permission['id'] ?>]" class="form-select form-select-sm"><option value="">Rôle par défaut: <?= htmlspecialchars($permission['code']) ?></option><option value="allow" <?= ($userSelected[(int)$permission['id']] ?? '') === 'allow' ? 'selected' : '' ?>>Autoriser: <?= htmlspecialchars($permission['code']) ?></option><option value="deny" <?= ($userSelected[(int)$permission['id']] ?? '') === 'deny' ? 'selected' : '' ?>>Refuser: <?= htmlspecialchars($permission['code']) ?></option></select></div><?php endforeach; ?></div><button class="btn btn-primary">Enregistrer les exceptions</button></form><?php endif; ?></div></div>
<div class="row g-4">
    <div class="col-lg-3"><div class="card"><div class="card-header">Rôle</div><div class="list-group list-group-flush">
        <?php foreach ($roles as $role): ?><a class="list-group-item list-group-item-action <?= (int)$role['id'] === $roleId ? 'active' : '' ?>" href="<?= Router::url('permissions?role=' . (int)$role['id']) ?>"><?= htmlspecialchars($role['nom']) ?></a><?php endforeach; ?>
    </div></div></div>
    <div class="col-lg-9"><form method="POST" action="<?= Router::url('permissions/save') ?>"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>"><input type="hidden" name="role_id" value="<?= (int)$roleId ?>">
        <div class="card"><div class="card-header d-flex justify-content-between"><span>Permissions</span><span><button type="button" class="btn btn-sm btn-outline-secondary" id="select-all">Tout sélectionner</button> <button type="button" class="btn btn-sm btn-outline-secondary" id="clear-all">Tout désélectionner</button></span></div><div class="card-body"><div class="row">
        <?php $module = ''; foreach ($permissions as $permission): if ($module !== $permission['module']): $module = $permission['module']; ?><div class="col-12 mt-2"><h6 class="text-uppercase text-muted border-bottom pb-2"><?= htmlspecialchars($module) ?></h6></div><?php endif; ?><div class="col-md-6"><label class="form-check mb-2"><input class="form-check-input permission-check" type="checkbox" name="permissions[]" value="<?= (int)$permission['id'] ?>" <?= in_array((int)$permission['id'], $selected, true) ? 'checked' : '' ?>> <?= htmlspecialchars($permission['nom']) ?> <small class="text-muted">(<?= htmlspecialchars($permission['code']) ?>)</small></label></div><?php endforeach; ?>
        </div></div><div class="card-footer text-end"><button class="btn btn-primary"><i class="bi bi-check2 me-1"></i>Enregistrer</button></div></div>
    </form></div>
</div>
<script>document.getElementById('select-all')?.addEventListener('click',()=>document.querySelectorAll('.permission-check').forEach(x=>x.checked=true));document.getElementById('clear-all')?.addEventListener('click',()=>document.querySelectorAll('.permission-check').forEach(x=>x.checked=false));</script>
