<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->e($siteTitle ?? 'Iglesia') ?></title>
    <?php if (!empty($tagline)): ?>
        <meta name="description" content="<?= $this->e($tagline) ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="/assets/theme/app.css">
    <?php do_action('front.head') ?>
</head>
<body class="bg-white text-gray-900 font-sans" x-data="{ mobileOpen: false }">

<header class="border-b border-gray-100">
    <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="/" class="text-lg font-bold text-brand-800"><?= $this->e($siteTitle ?? 'Iglesia') ?></a>

        <nav class="hidden md:flex items-center gap-6">
            <?php foreach (($primaryMenuItems ?? []) as $item): ?>
                <div class="relative" x-data="{ open: false }" @mouseleave="open = false">
                    <a href="<?= $this->e($item['url'] ?: '#') ?>"
                       @mouseenter="open = true"
                       class="text-sm font-medium text-gray-700 hover:text-brand-700">
                        <?= $this->e($item['label']) ?>
                    </a>
                    <?php if (!empty($item['children'])): ?>
                        <div x-show="open" x-cloak class="absolute left-0 mt-2 w-48 bg-white shadow-lg rounded-md border py-2 z-10">
                            <?php foreach ($item['children'] as $child): ?>
                                <a href="<?= $this->e($child['url'] ?: '#') ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <?= $this->e($child['label']) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </nav>

        <div class="flex items-center gap-3">
            <a href="/sermones" class="hidden sm:inline-block bg-brand-700 text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-brand-800">
                Ver en Vivo
            </a>
            <button class="md:hidden" @click="mobileOpen = !mobileOpen" aria-label="Menú">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>

    <nav x-show="mobileOpen" x-cloak class="md:hidden border-t border-gray-100 px-4 py-3 space-y-2">
        <?php foreach (($primaryMenuItems ?? []) as $item): ?>
            <a href="<?= $this->e($item['url'] ?: '#') ?>" class="block text-sm font-medium text-gray-700 py-1">
                <?= $this->e($item['label']) ?>
            </a>
        <?php endforeach; ?>
    </nav>
</header>
