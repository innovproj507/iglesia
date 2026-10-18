<?php $this->layout('admin::layout', ['title' => $title, 'currentUser' => $currentUser, 'menuItems' => $menuItems, 'activeMenu' => $activeMenu, 'eyebrow' => 'Equipo']) ?>
<?php $icon = fn (string $name, string $class = 'h-5 w-5') => $this->fetch('admin::partials/icon', ['name' => $name, 'class' => $class]); ?>

<div class="grid gap-6 lg:grid-cols-3">
    <div class="card overflow-hidden lg:col-span-2">
        <div class="overflow-x-auto">
            <table class="table">
                <thead><tr><th>Nombre</th><th>Rol</th><th class="text-right">Acciones</th></tr></thead>
                <tbody>
                    <?php foreach ($users as $i => $user): ?>
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full text-sm font-bold text-white <?= ['bg-brand-500', 'bg-violet-500', 'bg-earth-500', 'bg-rose-500'][$i % 4] ?>"><?= $this->e(mb_strtoupper(mb_substr($user['name'], 0, 1))) ?></span>
                                    <span>
                                        <span class="block font-bold text-gray-900 dark:text-white"><?= $this->e($user['name']) ?></span>
                                        <span class="block text-xs text-gray-400"><?= $this->e($user['email']) ?></span>
                                    </span>
                                </div>
                            </td>
                            <td><span class="badge-brand capitalize"><?= $this->e($user['role_name']) ?></span></td>
                            <td class="text-right">
                                <?php if ((int) $user['id'] !== (int) ($currentUser->id ?? 0)): ?>
                                    <form method="post" action="/admin/users/<?= (int) $user['id'] ?>/delete" onsubmit="return confirm('¿Eliminar usuario?')" class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="icon-btn h-9 w-9 hover:bg-red-50 hover:text-red-500" aria-label="Eliminar"><?= $icon('trash', 'h-4 w-4') ?></button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-xs text-gray-400">Tú</span>
                                <?php endif; ?>
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
            <div><label class="label">Nombre</label><input type="text" name="name" required class="input"></div>
            <div><label class="label">Correo</label><input type="email" name="email" required class="input"></div>
            <div><label class="label">Contraseña</label><input type="password" name="password" required class="input" autocomplete="new-password"></div>
            <div>
                <label class="label">Rol</label>
                <select name="role_id" class="input">
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= (int) $role['id'] ?>"><?= $this->e($role['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn-primary w-full">Crear usuario</button>
        </form>
    </div>
</div>
