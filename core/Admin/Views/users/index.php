<?php $this->layout('admin::layout', ['title' => $title, 'currentUser' => $currentUser, 'menuItems' => $menuItems, 'activeMenu' => $activeMenu, 'eyebrow' => 'Equipo', 'flash' => $flash]) ?>
<?php
$icon = fn (string $name, string $class = 'h-5 w-5') => $this->fetch('admin::partials/icon', ['name' => $name, 'class' => $class]);
$error = fn (string $field) => isset($errors[$field]) ? '<p class="mt-1 text-xs font-medium text-red-500">' . $this->e($errors[$field]) . '</p>' : '';
?>

<div class="grid gap-6 lg:grid-cols-3">
    <div class="card overflow-hidden lg:col-span-2">
        <div class="overflow-x-auto">
            <table class="table">
                <thead><tr><th>Nombre</th><th>Rol</th><th>Último acceso</th><th class="text-right">Acciones</th></tr></thead>
                <tbody>
                    <?php foreach ($users as $i => $user): ?>
                        <?php $isSelf = (int) $user['id'] === (int) ($currentUser->id ?? 0); ?>
                        <tr>
                            <td>
                                <a href="/admin/users/<?= (int) $user['id'] ?>/edit" class="flex items-center gap-3">
                                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full text-sm font-bold text-white <?= ['bg-brand-500', 'bg-violet-500', 'bg-earth-500', 'bg-rose-500'][$i % 4] ?>"><?= $this->e(mb_strtoupper(mb_substr($user['name'], 0, 1))) ?></span>
                                    <span>
                                        <span class="block font-bold text-gray-900 hover:text-brand-600 dark:text-white">
                                            <?= $this->e($user['name']) ?>
                                            <?php if ($isSelf): ?><span class="badge-gray ml-1">Tú</span><?php endif; ?>
                                        </span>
                                        <span class="block text-xs text-gray-400"><?= $this->e($user['email']) ?></span>
                                    </span>
                                </a>
                            </td>
                            <td>
                                <span class="badge-brand capitalize"><?= $this->e($user['role_name']) ?></span>
                                <?php if ($user['status'] === 'suspended'): ?><span class="badge-amber ml-1">Suspendido</span><?php endif; ?>
                            </td>
                            <td class="whitespace-nowrap text-xs text-gray-500"><?= $user['last_login_at'] ? $this->e(time_ago($user['last_login_at'])) : 'Nunca' ?></td>
                            <td>
                                <div class="flex items-center justify-end gap-1">
                                    <a href="/admin/users/<?= (int) $user['id'] ?>/edit" class="icon-btn h-9 w-9" title="Editar" aria-label="Editar <?= $this->e($user['name']) ?>"><?= $icon('pencil', 'h-4 w-4') ?></a>
                                    <?php if (!$isSelf): ?>
                                        <form method="post" action="/admin/users/<?= (int) $user['id'] ?>/delete" onsubmit="return confirm('¿Eliminar a <?= $this->e($user['name']) ?>?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="icon-btn h-9 w-9 hover:bg-red-50 hover:text-red-500" title="Eliminar" aria-label="Eliminar"><?= $icon('trash', 'h-4 w-4') ?></button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card h-fit p-6">
        <h3 class="card-title mb-5"><?= $icon('plus', 'h-5 w-5 text-brand-600') ?> Nuevo usuario</h3>
        <form method="post" action="/admin/users" class="space-y-4">
            <?= csrf_field() ?>
            <div><label class="label">Nombre</label><input type="text" name="name" value="<?= $this->e($old['name'] ?? '') ?>" required class="input"><?= $error('name') ?></div>
            <div><label class="label">Correo</label><input type="email" name="email" value="<?= $this->e($old['email'] ?? '') ?>" required class="input"><?= $error('email') ?></div>
            <div><label class="label">Contraseña</label><input type="password" name="password" required minlength="8" class="input" autocomplete="new-password"><?= $error('password') ?><p class="hint">Mínimo 8 caracteres.</p></div>
            <div>
                <label class="label">Rol</label>
                <select name="role_id" class="input">
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= (int) $role['id'] ?>" <?= (int) ($old['role_id'] ?? 0) === (int) $role['id'] ? 'selected' : '' ?>><?= $this->e($role['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <?= $error('role_id') ?>
            </div>
            <button type="submit" class="btn-primary w-full">Crear usuario</button>
        </form>
    </div>
</div>
