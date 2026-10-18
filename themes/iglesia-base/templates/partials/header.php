<?php
$liveUrl = option('live_url') ?: (($socialLinks ?? [])['youtube'] ?? null);
$whatsappUrl = whatsapp_url();
$logo = option('logo');
$phone = option('phone');
$email = option('email');
$address = option('address');
$isExternal = static fn (string $url): bool => (bool) preg_match('~^https?://~', $url);

// Contexto de la página para plugins (SEO, analítica…)
$headContext = [
    'post' => $post ?? $sermon ?? null,
    'pageTitle' => $pageTitle ?? null,
    'siteTitle' => $siteTitle ?? '',
    'tagline' => $tagline ?? '',
    'isHome' => ($_SERVER['REQUEST_URI'] ?? '/') === '/' || str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/?'),
];
$documentTitle = apply_filters('document_title', isset($pageTitle) ? $pageTitle . ' | ' . ($siteTitle ?? 'Iglesia') : ($siteTitle ?? 'Iglesia'), $headContext);
$documentDescription = apply_filters('document_description', $tagline ?? '', $headContext);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->e($documentTitle) ?></title>
    <?php if (!empty($documentDescription)): ?>
        <meta name="description" content="<?= $this->e($documentDescription) ?>">
    <?php endif; ?>
    <meta name="theme-color" content="#15565a">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;1,500&family=Raleway:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/theme/app.css">
    <script>try{if(localStorage.getItem('text-lg')==='1')document.documentElement.classList.add('text-lg-mode')}catch(e){}</script>
    <?php do_action('front.head', $headContext) ?>
</head>
<body class="bg-white text-gray-700 font-sans antialiased" x-data="{ mobileOpen: false, scrolled: false }" @scroll.window="scrolled = window.scrollY > 40">

<a href="#contenido" class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-[60] focus:bg-white focus:px-4 focus:py-2 focus:rounded">Saltar al contenido</a>

<!-- Barra superior: horarios + redes -->
<div class="bg-brand-900 text-brand-100 text-xs">
    <div class="container-site flex items-center justify-between gap-4 py-2">
        <div class="flex min-w-0 items-center gap-5">
            <?php if ($address): ?>
                <a href="<?= $this->e(maps_url()) ?>" target="_blank" rel="noopener" class="flex min-w-0 items-center gap-1.5 hover:text-white">
                    <svg class="h-4 w-4 shrink-0 text-brand-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.14-7.5 11.25-7.5 11.25S4.5 17.64 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                    <span class="truncate"><?= $this->e($address) ?></span>
                </a>
            <?php else: ?>
                <span class="truncate"><?= $this->e(implode(' | ', $serviceTimes ?? []) ?: 'Te esperamos este domingo') ?></span>
            <?php endif; ?>
            <?php if ($phone): ?>
                <a href="tel:<?= $this->e(preg_replace('/[^\d+]/', '', $phone)) ?>" class="hidden shrink-0 items-center gap-1.5 hover:text-white md:flex">
                    <svg class="h-4 w-4 text-brand-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.28 6.72 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.37c0-.52-.35-.97-.85-1.09l-4.42-1.1c-.44-.12-.9.05-1.17.41l-.97 1.29a1.13 1.13 0 0 1-1.21.38 12.04 12.04 0 0 1-7.14-7.14 1.13 1.13 0 0 1 .38-1.21l1.3-.97c.36-.27.52-.73.4-1.17l-1.1-4.42A1.13 1.13 0 0 0 5.37 2.25H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                    <?= $this->e($phone) ?>
                </a>
            <?php endif; ?>
            <?php if ($email): ?>
                <a href="mailto:<?= $this->e($email) ?>" class="hidden shrink-0 items-center gap-1.5 hover:text-white lg:flex">
                    <svg class="h-4 w-4 text-brand-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.24a2.25 2.25 0 0 1-1.07 1.92l-7.5 4.61a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.91V6.75"/></svg>
                    <?= $this->e($email) ?>
                </a>
            <?php endif; ?>
        </div>
        <div class="hidden sm:flex items-center gap-3">
            <?php $this->insert('partials/social-icons', ['socialLinks' => $socialLinks ?? [], 'size' => 'h-4 w-4', 'class' => 'text-brand-200 hover:text-white']) ?>
        </div>
    </div>
</div>

<header class="sticky top-0 z-40 bg-white/95 backdrop-blur transition-shadow" :class="scrolled ? 'shadow-md' : 'shadow-sm'">
    <div class="container-site flex items-center justify-between h-20">
        <?php if ($logo): ?>
        <a href="/" class="flex items-center" aria-label="<?= $this->e($siteTitle ?? 'Inicio') ?>">
            <img src="<?= $this->e($logo) ?>" alt="<?= $this->e($siteTitle ?? '') ?>" class="h-11 w-auto sm:h-12">
        </a>
        <?php else: ?>
        <a href="/" class="flex items-center gap-3 group">
            <span class="grid place-items-center h-11 w-11 rounded-xl bg-gradient-to-br from-brand-500 to-brand-800 text-white shadow-md group-hover:scale-105 transition">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 3v18M6 9h12"/></svg>
            </span>
            <span class="leading-tight">
                <span class="block text-lg font-extrabold text-brand-800 tracking-tight"><?= $this->e($siteTitle ?? 'Iglesia') ?></span>
                <?php if (!empty($tagline)): ?>
                    <span class="hidden xl:block text-xs text-gray-500"><?= $this->e($tagline) ?></span>
                <?php endif; ?>
            </span>
        </a>
        <?php endif; ?>

        <nav class="hidden lg:flex items-center gap-1" aria-label="Principal">
            <?php foreach (($primaryMenuItems ?? []) as $item): ?>
                <?php $hasChildren = !empty($item['children']); ?>
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" @keydown.escape="open = false" @focusout="if (!$el.contains($event.relatedTarget)) open = false">
                    <a href="<?= $this->e($item['url'] ?: '#') ?>"
                       <?php if ($hasChildren): ?>@focus="open = true" aria-haspopup="true" :aria-expanded="open"<?php endif; ?>
                       class="inline-flex items-center gap-1 px-4 py-2 rounded-full text-sm font-semibold text-gray-700 hover:text-brand-600 hover:bg-brand-50 transition">
                        <?= $this->e($item['label']) ?>
                        <?php if ($hasChildren): ?>
                            <svg class="h-4 w-4 transition" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7"/></svg>
                        <?php endif; ?>
                    </a>
                    <?php if ($hasChildren): ?>
                        <div x-show="open" x-cloak
                             x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                             class="absolute left-0 top-full pt-2 w-60 z-50">
                            <div class="bg-white rounded-xl shadow-xl ring-1 ring-black/5 py-2 border-t-4 border-brand-500">
                                <?php foreach ($item['children'] as $child): ?>
                                    <a href="<?= $this->e($child['url'] ?: '#') ?>" <?= $isExternal($child['url'] ?? '') ? 'target="_blank" rel="noopener"' : '' ?>
                                       class="block px-5 py-2.5 text-sm text-gray-600 hover:bg-brand-50 hover:text-brand-700 hover:pl-6 transition-all">
                                        <?= $this->e($child['label']) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </nav>

        <div class="flex items-center gap-2">
            <button type="button" @click="$store.a11y.toggle()" :aria-pressed="$store.a11y.large"
                    class="hidden sm:grid place-items-center h-10 w-10 rounded-full text-xs font-bold text-gray-500 hover:bg-brand-50 hover:text-brand-700 transition"
                    :class="$store.a11y.large && 'bg-brand-50 text-brand-700'" title="Agrandar texto" aria-label="Agrandar texto">
                <span>A<span class="text-base">A</span></span>
            </button>
            <?php if ($liveUrl): ?>
            <a href="<?= $this->e($liveUrl) ?>" <?= $isExternal($liveUrl) ? 'target="_blank" rel="noopener"' : '' ?>
               class="hidden sm:inline-flex items-center gap-2 rounded-full bg-red-600 px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-red-600/25 hover:bg-red-700 transition">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-white opacity-75"></span>
                    <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-white"></span>
                </span>
                En vivo
            </a>
            <?php elseif ($whatsappUrl): ?>
            <a href="<?= $this->e($whatsappUrl) ?>" target="_blank" rel="noopener"
               class="hidden sm:inline-flex items-center gap-2 rounded-full bg-brand-500 px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-brand-500/25 hover:bg-brand-600 transition">
                <?php $this->insert('partials/whatsapp-icon', ['class' => 'h-4 w-4']) ?>
                Escríbenos
            </a>
            <?php endif; ?>
            <button class="lg:hidden grid place-items-center h-11 w-11 rounded-full hover:bg-brand-50 text-brand-800" @click="mobileOpen = !mobileOpen" :aria-expanded="mobileOpen" aria-label="Menú">
                <svg x-show="!mobileOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="mobileOpen" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    <!-- Menú móvil -->
    <nav x-show="mobileOpen" x-cloak x-transition.opacity
         class="lg:hidden border-t border-gray-100 bg-white max-h-[75vh] overflow-y-auto" aria-label="Móvil">
        <div class="container-site py-4 space-y-1">
            <?php foreach (($primaryMenuItems ?? []) as $item): ?>
                <?php if (!empty($item['children'])): ?>
                    <div x-data="{ open: false }">
                        <button type="button" @click="open = !open" :aria-expanded="open" class="flex w-full items-center justify-between rounded-lg px-3 py-3 text-base font-semibold text-gray-800 hover:bg-brand-50">
                            <?= $this->e($item['label']) ?>
                            <svg class="h-5 w-5 transition" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m19 9-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-cloak class="ml-3 border-l-2 border-brand-100 pl-3">
                            <?php if ($item['url']): ?>
                                <a href="<?= $this->e($item['url']) ?>" class="block rounded-lg px-3 py-2 text-sm font-semibold text-brand-700"><?= $this->e($item['label']) ?></a>
                            <?php endif; ?>
                            <?php foreach ($item['children'] as $child): ?>
                                <a href="<?= $this->e($child['url'] ?: '#') ?>" class="block rounded-lg px-3 py-2 text-sm text-gray-600 hover:text-brand-700"><?= $this->e($child['label']) ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?= $this->e($item['url'] ?: '#') ?>" class="block rounded-lg px-3 py-3 text-base font-semibold text-gray-800 hover:bg-brand-50"><?= $this->e($item['label']) ?></a>
                <?php endif; ?>
            <?php endforeach; ?>
            <div class="flex items-center gap-3 pt-4">
                <?php if ($liveUrl): ?>
                    <a href="<?= $this->e($liveUrl) ?>" class="flex-1 btn bg-red-600 text-white hover:bg-red-700">● En vivo</a>
                <?php elseif ($whatsappUrl): ?>
                    <a href="<?= $this->e($whatsappUrl) ?>" target="_blank" rel="noopener" class="flex-1 btn bg-brand-500 text-white hover:bg-brand-600">Escríbenos</a>
                <?php endif; ?>
                <button type="button" @click="$store.a11y.toggle()" class="btn border-2 border-gray-200 text-gray-600" aria-label="Agrandar texto">AA</button>
            </div>
        </div>
    </nav>
</header>

<main id="contenido">
