<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->e($title ?? 'Admin') ?> · <?= $this->e(option('site_title', 'Iglesia CMS')) ?></title>
    <link rel="stylesheet" href="/assets/admin/app.css">
</head>
<body class="bg-gray-100 text-gray-900">
<div class="min-h-screen flex">
    <aside class="w-64 bg-gray-900 text-gray-200 flex-shrink-0">
        <div class="px-4 py-4 text-lg font-semibold text-white border-b border-gray-700">
            <?= $this->e(option('site_title', 'Iglesia CMS')) ?>
        </div>
        <nav class="py-2">
            <?php foreach ($menuItems ?? [] as $item): ?>
                <a href="<?= $this->e($item['url']) ?>"
                   class="block px-4 py-2 text-sm hover:bg-gray-800 <?= ($activeMenu ?? '') === $item['slug'] ? 'bg-gray-800 text-white' : '' ?>">
                    <?= $this->e($item['label']) ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <form method="post" action="/admin/logout" class="px-4 py-4 border-t border-gray-700 mt-2">
            <?= csrf_field() ?>
            <button type="submit" class="text-sm text-gray-400 hover:text-white">Cerrar sesión</button>
        </form>
    </aside>
    <main class="flex-1">
        <header class="bg-white border-b px-6 py-4 flex items-center justify-between">
            <h1 class="text-xl font-semibold"><?= $this->e($title ?? '') ?></h1>
            <?php if ($currentUser ?? null): ?>
                <span class="text-sm text-gray-500"><?= $this->e($currentUser->name) ?></span>
            <?php endif; ?>
        </header>
        <div class="p-6">
            <?php if ($flash ?? null): ?>
                <div class="mb-4 rounded bg-brand-50 border border-brand-300 text-brand-800 px-4 py-3 text-sm">
                    <?= $this->e($flash) ?>
                </div>
            <?php endif; ?>
            <?= $this->section('content') ?>
        </div>
    </main>
</div>
</body>
</html>
