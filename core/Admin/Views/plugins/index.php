<?php $this->layout('admin::layout', ['title' => $title, 'currentUser' => $currentUser, 'menuItems' => $menuItems, 'activeMenu' => $activeMenu]) ?>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <?php foreach ($manifests as $slug => $manifest): ?>
        <?php $active = in_array($slug, $activeSlugs, true); ?>
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h3 class="font-medium"><?= $this->e($manifest->name) ?></h3>
                    <p class="text-xs text-gray-400">v<?= $this->e($manifest->version) ?> · <?= $this->e($manifest->author) ?></p>
                </div>
                <span class="text-xs px-2 py-1 rounded <?= $active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' ?>">
                    <?= $active ? 'Activo' : 'Inactivo' ?>
                </span>
            </div>
            <p class="text-sm text-gray-600 mb-4"><?= $this->e($manifest->description) ?></p>
            <form method="post" action="/admin/plugins/<?= $this->e($slug) ?>/<?= $active ? 'deactivate' : 'activate' ?>">
                <?= csrf_field() ?>
                <button type="submit" class="text-sm px-4 py-2 rounded <?= $active ? 'bg-red-50 text-red-700 hover:bg-red-100' : 'bg-brand-700 text-white hover:bg-brand-800' ?>">
                    <?= $active ? 'Desactivar' : 'Activar' ?>
                </button>
            </form>
        </div>
    <?php endforeach; ?>
    <?php if (empty($manifests)): ?>
        <p class="text-gray-400 col-span-full text-center py-6">No hay plugins instalados en /plugins.</p>
    <?php endif; ?>
</div>
