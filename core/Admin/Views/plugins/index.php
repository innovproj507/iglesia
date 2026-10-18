<?php $this->layout('admin::layout', ['title' => $title, 'currentUser' => $currentUser, 'menuItems' => $menuItems, 'activeMenu' => $activeMenu, 'eyebrow' => 'Extensiones']) ?>
<?php $icon = fn (string $name, string $class = 'h-5 w-5') => $this->fetch('admin::partials/icon', ['name' => $name, 'class' => $class]); ?>

<div class="grid gap-6 md:grid-cols-2 2xl:grid-cols-3">
    <?php foreach ($manifests as $slug => $manifest): ?>
        <?php $active = in_array($slug, $activeSlugs, true); ?>
        <div class="card flex flex-col p-6">
            <div class="mb-4 flex items-start gap-4">
                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl <?= $active ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10' : 'bg-gray-100 text-gray-400 dark:bg-slate-800' ?>"><?= $icon('puzzle', 'h-6 w-6') ?></span>
                <div class="flex-1">
                    <h3 class="font-bold text-gray-900 dark:text-white"><?= $this->e($manifest->name) ?></h3>
                    <p class="text-xs text-gray-400">v<?= $this->e($manifest->version) ?> · <?= $this->e($manifest->author) ?></p>
                </div>
                <span class="<?= $active ? 'badge-green' : 'badge-gray' ?>"><?= $active ? 'Activo' : 'Inactivo' ?></span>
            </div>
            <p class="mb-5 flex-1 text-sm text-gray-600 dark:text-slate-400"><?= $this->e($manifest->description) ?></p>
            <form method="post" action="/admin/plugins/<?= $this->e($slug) ?>/<?= $active ? 'deactivate' : 'activate' ?>">
                <?= csrf_field() ?>
                <button type="submit" class="<?= $active ? 'btn-danger' : 'btn-primary' ?> w-full"><?= $active ? 'Desactivar' : 'Activar' ?></button>
            </form>
        </div>
    <?php endforeach; ?>
    <?php if (empty($manifests)): ?>
        <p class="col-span-full py-16 text-center text-gray-400">No hay plugins instalados en /plugins.</p>
    <?php endif; ?>
</div>
