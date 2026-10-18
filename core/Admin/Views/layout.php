<?php
$siteTitle = option('site_title', 'Iglesia CMS');
$logo = option('logo');
$draftCount = db()->count('posts', ['status' => 'draft']);
$draftsByType = db()->pdo()->query("SELECT post_type, COUNT(*) FROM posts WHERE status = 'draft' GROUP BY post_type")->fetchAll(PDO::FETCH_KEY_PAIR);
$userName = $currentUser->name ?? 'Usuario';
$initials = mb_strtoupper(implode('', array_map(fn ($w) => mb_substr($w, 0, 1), array_slice(preg_split('/\s+/', trim($userName)), 0, 2))));
$icon = fn (string $name, string $class = 'h-5 w-5') => $this->fetch('admin::partials/icon', ['name' => $name, 'class' => $class]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->e($title ?? 'Admin') ?> · <?= $this->e($siteTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@1,500&display=swap" rel="stylesheet">
    <script>
        try {
            var t = localStorage.getItem('admin-theme');
            if (t === 'dark' || (!t && matchMedia('(prefers-color-scheme: dark)').matches)) document.documentElement.classList.add('dark');
        } catch (e) {}
    </script>
    <link rel="stylesheet" href="/assets/admin/app.css">
</head>
<body class="font-sans">
<div class="flex min-h-screen">

    <!-- Fondo para el menú móvil -->
    <div id="sidebar-backdrop" class="fixed inset-0 z-30 hidden bg-slate-900/40 backdrop-blur-sm lg:hidden" onclick="toggleSidebar(false)"></div>

    <!-- Barra lateral -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full flex-col border-r border-gray-100 bg-white transition-transform duration-200 dark:border-slate-800 dark:bg-slate-900 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0">
        <div class="flex h-20 items-center gap-3 px-6">
            <?php if ($logo): ?>
                <img src="<?= $this->e($logo) ?>" alt="" class="h-10 w-auto dark:brightness-0 dark:invert">
            <?php else: ?>
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-brand-600 text-white"><?= $icon('cross') ?></span>
                <span class="min-w-0">
                    <span class="block truncate font-extrabold text-gray-900 dark:text-white"><?= $this->e($siteTitle) ?></span>
                    <span class="block text-xs text-gray-400">Panel de administración</span>
                </span>
            <?php endif; ?>
            <button type="button" class="icon-btn ml-auto lg:hidden" onclick="toggleSidebar(false)" aria-label="Cerrar menú"><?= $icon('x') ?></button>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-4" aria-label="Administración">
            <?php foreach ($menuItems ?? [] as $item): ?>
                <?php
                $active = ($activeMenu ?? '') === $item['slug'];
                parse_str((string) parse_url($item['url'], PHP_URL_QUERY), $query);
                $badge = (int) ($draftsByType[$query['type'] ?? ''] ?? 0);
                ?>
                <a href="<?= $this->e($item['url']) ?>" class="nav-link <?= $active ? 'is-active' : '' ?>" <?= $active ? 'aria-current="page"' : '' ?>>
                    <?= $icon($item['icon'] ?? 'file') ?>
                    <span class="flex-1"><?= $this->e($item['label']) ?></span>
                    <?php if ($badge > 0): ?>
                        <span class="grid h-5 min-w-[1.25rem] place-items-center rounded-full px-1.5 text-[11px] font-bold <?= $active ? 'bg-white/20 text-white' : 'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-300' ?>"><?= $badge ?></span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="p-4">
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-brand-50 to-earth-50 p-5 dark:from-slate-800 dark:to-slate-800">
                <svg class="absolute -right-3 -top-3 h-24 w-24 text-brand-200/70 dark:text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path d="M12 3v18M6 9h12"/></svg>
                <p class="relative font-serif text-lg italic leading-snug text-gray-800 dark:text-slate-200">“Todo lo puedo en Cristo que me fortalece.”</p>
                <p class="relative mt-1 text-xs font-semibold text-gray-400">Filipenses 4:13</p>
                <a href="/" target="_blank" class="relative mt-4 inline-flex items-center gap-2 rounded-xl bg-brand-600 px-4 py-2 text-xs font-bold text-white hover:bg-brand-700">
                    Ver sitio <?= $icon('arrow-right', 'h-3.5 w-3.5') ?>
                </a>
            </div>
        </div>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col">
        <!-- Barra superior -->
        <header class="sticky top-0 z-20 border-b border-gray-100 bg-[#f3f6f5]/80 backdrop-blur dark:border-slate-800 dark:bg-slate-950/80">
            <div class="flex h-20 items-center gap-3 px-4 sm:px-8">
                <button type="button" class="icon-btn lg:hidden" onclick="toggleSidebar(true)" aria-label="Abrir menú"><?= $icon('menu') ?></button>

                <form action="/admin/posts" method="get" class="relative hidden max-w-xl flex-1 sm:block" role="search">
                    <input type="hidden" name="type" value="all">
                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"><?= $icon('search', 'h-4.5 w-4.5 h-[18px] w-[18px]') ?></span>
                    <input type="search" name="q" value="<?= $this->e($_GET['q'] ?? '') ?>" placeholder="Buscar sermones, entradas, páginas…"
                           class="w-full rounded-2xl border-0 bg-white py-3 pl-11 pr-4 text-sm shadow-soft ring-1 ring-gray-100 placeholder:text-gray-400 focus:ring-2 focus:ring-brand-500 dark:bg-slate-900 dark:ring-slate-800">
                </form>

                <div class="ml-auto flex items-center gap-1 sm:gap-2">
                    <a href="/admin/posts?type=all&status=draft" class="icon-btn" title="Borradores pendientes" aria-label="Borradores pendientes: <?= $draftCount ?>">
                        <?= $icon('bell') ?>
                        <?php if ($draftCount > 0): ?>
                            <span class="absolute right-1.5 top-1.5 grid h-4 min-w-[1rem] place-items-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white ring-2 ring-[#f3f6f5] dark:ring-slate-950"><?= $draftCount ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="/" target="_blank" class="icon-btn" title="Ver sitio" aria-label="Ver sitio"><?= $icon('globe') ?></a>
                    <button type="button" class="icon-btn" onclick="toggleTheme()" title="Cambiar tema" aria-label="Cambiar tema">
                        <span class="dark:hidden"><?= $icon('moon') ?></span>
                        <span class="hidden dark:inline"><?= $icon('sun') ?></span>
                    </button>

                    <span class="mx-2 hidden h-8 w-px bg-gray-200 dark:bg-slate-800 sm:block"></span>

                    <details class="relative">
                        <summary class="flex cursor-pointer list-none items-center gap-3 rounded-2xl p-1 pr-2 hover:bg-white dark:hover:bg-slate-900 [&::-webkit-details-marker]:hidden">
                            <span class="grid h-10 w-10 place-items-center rounded-full bg-gradient-to-br from-brand-400 to-brand-700 text-sm font-bold text-white ring-2 ring-white dark:ring-slate-900"><?= $this->e($initials) ?></span>
                            <span class="hidden text-left leading-tight md:block">
                                <span class="block text-sm font-bold text-gray-900 dark:text-white"><?= $this->e($userName) ?></span>
                                <span class="block text-xs text-gray-400"><?= $this->e($currentUser->email ?? '') ?></span>
                            </span>
                            <span class="text-gray-400"><?= $icon('chevron-down', 'h-4 w-4') ?></span>
                        </summary>
                        <div class="absolute right-0 mt-2 w-56 overflow-hidden rounded-2xl border border-gray-100 bg-white py-2 shadow-xl dark:border-slate-800 dark:bg-slate-900">
                            <a href="/admin/profile" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-slate-800"><?= $icon('users', 'h-4 w-4') ?> Mi perfil</a>
                            <a href="/" target="_blank" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-slate-800"><?= $icon('globe', 'h-4 w-4') ?> Ver sitio</a>
                            <?php if ($currentUser && $currentUser->can('manage_options')): ?>
                                <a href="/admin/settings" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-slate-800"><?= $icon('settings', 'h-4 w-4') ?> Ajustes</a>
                            <?php endif; ?>
                            <form method="post" action="/admin/logout" class="mt-1 border-t border-gray-100 pt-1 dark:border-slate-800">
                                <?= csrf_field() ?>
                                <button type="submit" class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10"><?= $icon('logout', 'h-4 w-4') ?> Cerrar sesión</button>
                            </form>
                        </div>
                    </details>
                </div>
            </div>
        </header>

        <main class="flex-1 px-4 py-6 sm:px-8 sm:py-8">
            <?php if (empty($hideTitle)): ?>
                <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <?php if (!empty($eyebrow)): ?>
                            <p class="text-xs font-bold uppercase tracking-widest text-brand-600 dark:text-brand-300"><?= $this->e($eyebrow) ?></p>
                        <?php endif; ?>
                        <h1 class="text-2xl font-extrabold tracking-tight text-gray-900 dark:text-white"><?= $this->e($title ?? '') ?></h1>
                    </div>
                    <div class="flex flex-wrap items-center gap-2"><?= $this->section('actions') ?></div>
                </div>
            <?php endif; ?>

            <?php do_action('admin.notices') ?>

            <?php if ($flash ?? null): ?>
                <div class="mb-6 flex items-center gap-3 rounded-2xl border border-brand-200 bg-brand-50 px-5 py-4 text-sm font-medium text-brand-800 dark:border-brand-500/20 dark:bg-brand-500/10 dark:text-brand-200">
                    <?= $icon('check') ?> <?= $this->e($flash) ?>
                </div>
            <?php endif; ?>

            <?= $this->section('content') ?>
        </main>
    </div>
</div>

<script>
    function toggleSidebar(open) {
        document.getElementById('sidebar').classList.toggle('-translate-x-full', !open);
        document.getElementById('sidebar-backdrop').classList.toggle('hidden', !open);
    }
    function toggleTheme() {
        var dark = document.documentElement.classList.toggle('dark');
        try { localStorage.setItem('admin-theme', dark ? 'dark' : 'light'); } catch (e) {}
    }
    // Cierra el menú de usuario al hacer clic fuera
    document.addEventListener('click', function (e) {
        document.querySelectorAll('details[open]').forEach(function (d) { if (!d.contains(e.target)) d.removeAttribute('open'); });
    });
</script>
</body>
</html>
