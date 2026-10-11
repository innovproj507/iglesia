<?php $this->layout('admin::layout', ['title' => $title, 'currentUser' => $currentUser, 'menuItems' => $menuItems, 'activeMenu' => $activeMenu]) ?>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <?php foreach ($themes as $slug => $theme): ?>
        <?php $active = $slug === $activeSlug; ?>
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h3 class="font-medium"><?= $this->e($theme['name'] ?? $slug) ?></h3>
                    <p class="text-xs text-gray-400">v<?= $this->e($theme['version'] ?? '1.0.0') ?></p>
                </div>
                <?php if ($active): ?>
                    <span class="text-xs px-2 py-1 rounded bg-green-100 text-green-700">Activo</span>
                <?php endif; ?>
            </div>
            <p class="text-sm text-gray-600 mb-4"><?= $this->e($theme['description'] ?? '') ?></p>
            <?php if (!$active): ?>
                <form method="post" action="/admin/themes/<?= $this->e($slug) ?>/activate">
                    <?= csrf_field() ?>
                    <button type="submit" class="text-sm px-4 py-2 rounded bg-brand-700 text-white hover:bg-brand-800">Activar</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
