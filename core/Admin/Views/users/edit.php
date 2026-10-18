<?php $this->layout('admin::layout', ['title' => $title, 'currentUser' => $currentUser, 'menuItems' => $menuItems, 'activeMenu' => $activeMenu, 'eyebrow' => 'Equipo']) ?>
<?php
$icon = fn (string $name, string $class = 'h-5 w-5') => $this->fetch('admin::partials/icon', ['name' => $name, 'class' => $class]);
$error = fn (string $field) => isset($errors[$field]) ? '<p class="mt-1 text-xs font-medium text-red-500">' . $this->e($errors[$field]) . '</p>' : '';
?>

<?php $this->start('actions') ?>
    <a href="/admin/users" class="btn-secondary">← Volver</a>
<?php $this->stop() ?>

<form method="post" action="/admin/users/<?= (int) $user['id'] ?>" class="grid max-w-5xl gap-6 lg:grid-cols-3">
    <?= csrf_field() ?>

    <div class="card flex h-fit flex-col items-center p-6 text-center">
        <span class="grid h-20 w-20 place-items-center rounded-full bg-gradient-to-br from-brand-400 to-brand-700 text-2xl font-bold text-white"><?= $this->e(mb_strtoupper(mb_substr($user['name'], 0, 1))) ?></span>
        <p class="mt-4 font-bold text-gray-900 dark:text-white"><?= $this->e($user['name']) ?></p>
        <p class="text-sm text-gray-400"><?= $this->e($user['email']) ?></p>
        <dl class="mt-6 w-full space-y-2 rounded-xl bg-gray-50 p-4 text-left text-xs dark:bg-slate-800/60">
            <div class="flex justify-between"><dt class="text-gray-400">Creado</dt><dd class="font-semibold"><?= $this->e(format_date($user['created_at'])) ?></dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Último acceso</dt><dd class="font-semibold"><?= $user['last_login_at'] ? $this->e(time_ago($user['last_login_at'])) : 'Nunca' ?></dd></div>
        </dl>
    </div>

    <div class="space-y-6 lg:col-span-2">
        <section class="card space-y-5 p-6">
            <h2 class="card-title">Datos</h2>
            <div class="grid gap-5 sm:grid-cols-2">
                <div><label class="label">Nombre</label><input type="text" name="name" value="<?= $this->e($values['name']) ?>" required class="input"><?= $error('name') ?></div>
                <div><label class="label">Correo</label><input type="email" name="email" value="<?= $this->e($values['email']) ?>" required class="input"><?= $error('email') ?></div>
                <div>
                    <label class="label">Rol</label>
                    <select name="role_id" class="input" <?= $isSelf ? 'disabled' : '' ?>>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= (int) $role['id'] ?>" <?= (int) $values['role_id'] === (int) $role['id'] ? 'selected' : '' ?>><?= $this->e($role['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($isSelf): ?><input type="hidden" name="role_id" value="<?= (int) $values['role_id'] ?>"><p class="hint">No puedes cambiar tu propio rol.</p><?php endif; ?>
                    <?= $error('role_id') ?>
                </div>
                <div>
                    <label class="label">Estado</label>
                    <select name="status" class="input" <?= $isSelf ? 'disabled' : '' ?>>
                        <option value="active" <?= $values['status'] === 'active' ? 'selected' : '' ?>>Activo</option>
                        <option value="suspended" <?= $values['status'] === 'suspended' ? 'selected' : '' ?>>Suspendido (no puede entrar)</option>
                    </select>
                </div>
            </div>
        </section>

        <section class="card space-y-5 p-6">
            <div>
                <h2 class="card-title">Cambiar contraseña</h2>
                <p class="mt-1 text-xs text-gray-400">Déjalo vacío para mantener la contraseña actual.</p>
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <div><label class="label">Nueva contraseña</label><input type="password" name="password" minlength="8" class="input" autocomplete="new-password"><?= $error('password') ?></div>
                <div><label class="label">Confirmar contraseña</label><input type="password" name="password_confirmation" class="input" autocomplete="new-password"><?= $error('password_confirmation') ?></div>
            </div>
        </section>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary px-8"><?= $icon('check', 'h-4 w-4') ?> Guardar cambios</button>
        </div>
    </div>
</form>
