<?php $this->layout('admin::layout', ['title' => $title, 'currentUser' => $currentUser, 'menuItems' => $menuItems, 'activeMenu' => $activeMenu]) ?>

<div class="grid grid-cols-3 gap-6">
    <div class="col-span-1 bg-white rounded-lg shadow p-5">
        <h3 class="font-medium mb-3">Menús</h3>
        <ul class="space-y-1 mb-4">
            <?php foreach ($menus as $menu): ?>
                <li>
                    <a href="/admin/menus?menu_id=<?= (int) $menu['id'] ?>"
                       class="block px-2 py-1 rounded text-sm <?= (int) $activeMenuId === (int) $menu['id'] ? 'bg-brand-50 text-brand-800' : 'hover:bg-gray-50' ?>">
                        <?= $this->e($menu['name']) ?> <?= $menu['location'] ? '(' . $this->e($menu['location']) . ')' : '' ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <form method="post" action="/admin/menus" class="space-y-2">
            <?= csrf_field() ?>
            <input type="text" name="name" placeholder="Nombre del menú" required class="w-full border rounded px-3 py-2 text-sm">
            <input type="text" name="location" placeholder="Ubicación (primary, footer...)" class="w-full border rounded px-3 py-2 text-sm">
            <button type="submit" class="w-full bg-brand-700 text-white rounded py-2 text-sm hover:bg-brand-800">Crear menú</button>
        </form>
    </div>

    <div class="col-span-2 bg-white rounded-lg shadow p-5">
        <h3 class="font-medium mb-3">Elementos</h3>
        <ul class="space-y-2 mb-4">
            <?php foreach ($items as $item): ?>
                <li class="flex justify-between items-center border rounded px-3 py-2 text-sm">
                    <span><?= $this->e($item['label']) ?> <span class="text-gray-400">— <?= $this->e($item['url']) ?></span></span>
                    <form method="post" action="/admin/menus/items/<?= (int) $item['id'] ?>/delete">
                        <?= csrf_field() ?>
                        <input type="hidden" name="menu_id" value="<?= (int) $activeMenuId ?>">
                        <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                    </form>
                </li>
            <?php endforeach; ?>
            <?php if (empty($items)): ?>
                <li class="text-gray-400">Sin elementos.</li>
            <?php endif; ?>
        </ul>
        <?php if ($activeMenuId): ?>
        <form method="post" action="/admin/menus/items" class="flex gap-2">
            <?= csrf_field() ?>
            <input type="hidden" name="menu_id" value="<?= (int) $activeMenuId ?>">
            <input type="text" name="label" placeholder="Etiqueta" required class="flex-1 border rounded px-3 py-2 text-sm">
            <input type="text" name="url" placeholder="/ruta" class="flex-1 border rounded px-3 py-2 text-sm">
            <button type="submit" class="bg-brand-700 text-white px-4 py-2 rounded text-sm hover:bg-brand-800">Agregar</button>
        </form>
        <?php endif; ?>
    </div>
</div>
