<?php $this->layout('admin::layout', ['title' => $title, 'currentUser' => $currentUser, 'menuItems' => $menuItems, 'activeMenu' => $activeMenu]) ?>

<div class="bg-white rounded-lg shadow p-5 mb-6">
    <h3 class="font-medium mb-3">Nuevo usuario</h3>
    <form method="post" action="/admin/users" class="grid grid-cols-4 gap-3">
        <?= csrf_field() ?>
        <input type="text" name="name" placeholder="Nombre" required class="border rounded px-3 py-2 text-sm">
        <input type="email" name="email" placeholder="Correo" required class="border rounded px-3 py-2 text-sm">
        <input type="password" name="password" placeholder="Contraseña" required class="border rounded px-3 py-2 text-sm">
        <select name="role_id" class="border rounded px-3 py-2 text-sm">
            <?php foreach ($roles as $role): ?>
                <option value="<?= (int) $role['id'] ?>"><?= $this->e($role['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="col-span-4 bg-brand-700 text-white rounded py-2 text-sm hover:bg-brand-800">Crear usuario</button>
    </form>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm text-left">
        <thead class="bg-gray-50 text-gray-600">
            <tr><th class="px-4 py-2">Nombre</th><th class="px-4 py-2">Correo</th><th class="px-4 py-2">Rol</th><th class="px-4 py-2"></th></tr>
        </thead>
        <tbody class="divide-y">
            <?php foreach ($users as $user): ?>
                <tr>
                    <td class="px-4 py-2"><?= $this->e($user['name']) ?></td>
                    <td class="px-4 py-2"><?= $this->e($user['email']) ?></td>
                    <td class="px-4 py-2"><?= $this->e($user['role_name']) ?></td>
                    <td class="px-4 py-2 text-right">
                        <form method="post" action="/admin/users/<?= (int) $user['id'] ?>/delete" onsubmit="return confirm('¿Eliminar usuario?')">
                            <?= csrf_field() ?>
                            <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
