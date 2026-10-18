<?php $this->layout('admin::layout', ['title' => $title, 'currentUser' => $currentUser, 'menuItems' => $menuItems, 'activeMenu' => $activeMenu, 'eyebrow' => 'Apariencia']) ?>
<?php $icon = fn (string $name, string $class = 'h-5 w-5') => $this->fetch('admin::partials/icon', ['name' => $name, 'class' => $class]); ?>

<div class="grid gap-6 md:grid-cols-2 2xl:grid-cols-3">
    <?php foreach ($themes as $slug => $theme): ?>
        <?php $active = $slug === $activeSlug; ?>
        <div class="card flex flex-col overflow-hidden <?= $active ? 'ring-2 ring-brand-500' : '' ?>">
            <div class="relative h-36 bg-gradient-to-br from-brand-500 via-brand-800 to-brand-950">
                <svg class="absolute inset-0 h-full w-full opacity-10" aria-hidden="true"><defs><pattern id="t-<?= $this->e($slug) ?>" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M20 10v20M14 16h12" stroke="white" stroke-width="2" fill="none"/></pattern></defs><rect width="100%" height="100%" fill="url(#t-<?= $this->e($slug) ?>)"/></svg>
                <?php if ($active): ?><span class="badge absolute right-4 top-4 bg-white text-brand-700">✓ Activo</span><?php endif; ?>
            </div>
            <div class="flex flex-1 flex-col p-6">
                <h3 class="font-bold text-gray-900 dark:text-white"><?= $this->e($theme['name'] ?? $slug) ?></h3>
                <p class="text-xs text-gray-400">v<?= $this->e($theme['version'] ?? '1.0.0') ?></p>
                <p class="mt-3 flex-1 text-sm text-gray-600 dark:text-slate-400"><?= $this->e($theme['description'] ?? '') ?></p>
                <?php if (!$active): ?>
                    <form method="post" action="/admin/themes/<?= $this->e($slug) ?>/activate" class="mt-5">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn-primary w-full">Activar</button>
                    </form>
                <?php else: ?>
                    <a href="/" target="_blank" class="btn-secondary mt-5 w-full"><?= $icon('external', 'h-4 w-4') ?> Ver sitio</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
