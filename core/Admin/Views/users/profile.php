<?php $this->layout('admin::layout', ['title' => $title, 'currentUser' => $currentUser, 'menuItems' => $menuItems, 'activeMenu' => $activeMenu, 'eyebrow' => 'Tu cuenta', 'flash' => $flash]) ?>
<?php
$icon = fn (string $name, string $class = 'h-5 w-5') => $this->fetch('admin::partials/icon', ['name' => $name, 'class' => $class]);
$error = fn (string $field) => isset($errors[$field]) ? '<p class="mt-1 text-xs font-medium text-red-500">' . $this->e($errors[$field]) . '</p>' : '';
?>

<form method="post" action="/admin/profile" class="grid max-w-5xl gap-6 lg:grid-cols-3">
    <?= csrf_field() ?>

    <div class="card flex h-fit flex-col items-center p-6 text-center">
        <span class="grid h-20 w-20 place-items-center rounded-full bg-gradient-to-br from-brand-400 to-brand-700 text-2xl font-bold text-white"><?= $this->e(mb_strtoupper(mb_substr($user['name'], 0, 1))) ?></span>
        <p class="mt-4 font-bold text-gray-900 dark:text-white"><?= $this->e($user['name']) ?></p>
        <p class="text-sm text-gray-400"><?= $this->e($user['email']) ?></p>
        <span class="badge-brand mt-3 capitalize"><?= $this->e($roleName) ?></span>
    </div>

    <div class="space-y-6 lg:col-span-2">
        <section class="card space-y-5 p-6">
            <h2 class="card-title">Tus datos</h2>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="label">Nombre</label>
                    <input type="text" name="name" value="<?= $this->e($user['name']) ?>" required class="input">
                    <?= $error('name') ?>
                    <p class="hint">Es el nombre del saludo en el dashboard.</p>
                </div>
                <div>
                    <label class="label">Correo</label>
                    <input type="email" name="email" value="<?= $this->e($user['email']) ?>" required class="input">
                    <?= $error('email') ?>
                    <p class="hint">Con este correo inicias sesión.</p>
                </div>
            </div>
        </section>

        <section class="card space-y-5 p-6">
            <div>
                <h2 class="card-title">Cambiar contraseña</h2>
                <p class="mt-1 text-xs text-gray-400">Déjalo vacío si no quieres cambiarla.</p>
            </div>
            <div><label class="label">Contraseña actual</label><input type="password" name="current_password" class="input sm:max-w-xs" autocomplete="current-password"><?= $error('current_password') ?></div>
            <div class="grid gap-5 sm:grid-cols-2">
                <div><label class="label">Nueva contraseña</label><input type="password" name="password" minlength="8" class="input" autocomplete="new-password"><?= $error('password') ?><p class="hint">Mínimo 8 caracteres.</p></div>
                <div><label class="label">Confirmar nueva contraseña</label><input type="password" name="password_confirmation" class="input" autocomplete="new-password"><?= $error('password_confirmation') ?></div>
            </div>
        </section>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary px-8"><?= $icon('check', 'h-4 w-4') ?> Guardar</button>
        </div>
    </div>
</form>
