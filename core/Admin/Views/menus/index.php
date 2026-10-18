<?php $this->layout('admin::layout', ['title' => $title, 'currentUser' => $currentUser, 'menuItems' => $menuItems, 'activeMenu' => $activeMenu, 'eyebrow' => 'Apariencia']) ?>
<?php $icon = fn (string $name, string $class = 'h-5 w-5') => $this->fetch('admin::partials/icon', ['name' => $name, 'class' => $class]); ?>

<div class="grid gap-6 lg:grid-cols-3">
    <div class="card p-5">
        <h3 class="card-title mb-4">Menús</h3>
        <ul class="mb-5 space-y-1">
            <?php foreach ($menus as $menu): ?>
                <?php $on = (int) $activeMenuId === (int) $menu['id']; ?>
                <li>
                    <a href="/admin/menus?menu_id=<?= (int) $menu['id'] ?>" class="nav-link <?= $on ? 'is-active' : '' ?>">
                        <?= $icon('menu', 'h-4 w-4') ?>
                        <span class="flex-1"><?= $this->e($menu['name']) ?></span>
                        <?php if ($menu['location']): ?><span class="text-[11px] opacity-70"><?= $this->e($menu['location']) ?></span><?php endif; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <form method="post" action="/admin/menus" class="space-y-3 border-t border-gray-100 pt-5 dark:border-slate-800">
            <?= csrf_field() ?>
            <input type="text" name="name" placeholder="Nombre del menú" required class="input">
            <input type="text" name="location" placeholder="Ubicación (primary, footer…)" class="input">
            <button type="submit" class="btn-secondary w-full"><?= $icon('plus', 'h-4 w-4') ?> Crear menú</button>
        </form>
    </div>

    <div class="card p-5 lg:col-span-2">
        <h3 class="card-title mb-4">Elementos</h3>
        <ul class="mb-5 space-y-2">
            <?php foreach ($items as $item): ?>
                <li class="flex items-center gap-3 rounded-xl border border-gray-100 px-4 py-3 dark:border-slate-800">
                    <span class="text-gray-300"><?= $icon('menu', 'h-4 w-4') ?></span>
                    <span class="flex-1 text-sm">
                        <span class="font-bold text-gray-900 dark:text-white"><?= $this->e($item['label']) ?></span>
                        <span class="ml-2 text-gray-400"><?= $this->e($item['url']) ?></span>
                    </span>
                    <form method="post" action="/admin/menus/items/<?= (int) $item['id'] ?>/delete">
                        <?= csrf_field() ?>
                        <input type="hidden" name="menu_id" value="<?= (int) $activeMenuId ?>">
                        <button type="submit" class="icon-btn h-8 w-8 hover:bg-red-50 hover:text-red-500" aria-label="Eliminar"><?= $icon('trash', 'h-4 w-4') ?></button>
                    </form>
                </li>
            <?php endforeach; ?>
            <?php if (empty($items)): ?>
                <li class="rounded-xl bg-gray-50 px-4 py-6 text-center text-sm text-gray-400 dark:bg-slate-800/60">Sin elementos.</li>
            <?php endif; ?>
        </ul>
        <?php if ($activeMenuId): ?>
            <form method="post" action="/admin/menus/items" class="grid gap-3 border-t border-gray-100 pt-5 dark:border-slate-800 sm:grid-cols-[1fr_1fr_auto]">
                <?= csrf_field() ?>
                <input type="hidden" name="menu_id" value="<?= (int) $activeMenuId ?>">
                <input type="text" name="label" placeholder="Etiqueta" required class="input">
                <input type="text" name="url" placeholder="/ruta o https://…" class="input">
                <button type="submit" class="btn-primary"><?= $icon('plus', 'h-4 w-4') ?> Agregar</button>
            </form>
        <?php endif; ?>
    </div>
</div>
