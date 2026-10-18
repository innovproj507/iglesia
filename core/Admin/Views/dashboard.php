<?php $this->layout('admin::layout', ['title' => $title, 'currentUser' => $currentUser, 'menuItems' => $menuItems, 'activeMenu' => $activeMenu, 'hideTitle' => true]) ?>
<?php
$icon = fn (string $name, string $class = 'h-5 w-5') => $this->fetch('admin::partials/icon', ['name' => $name, 'class' => $class]);
$hour = (int) $now->format('G');
$greeting = $hour < 12 ? 'Buenos días' : ($hour < 19 ? 'Buenas tardes' : 'Buenas noches');
$firstName = explode(' ', trim($currentUser->name ?? ''))[0] ?: 'bienvenido';
$days = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
$months = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
$longDate = fn (DateTimeImmutable $d) => ucfirst($days[(int) $d->format('w')]) . ', ' . $d->format('j') . ' de ' . $months[(int) $d->format('n') - 1];
$heroImage = option_array('welcome_images')[0] ?? option('hero_image');
$dotColors = ['bg-brand-500', 'bg-violet-500', 'bg-earth-500', 'bg-rose-500', 'bg-amber-500'];
$types = [
    'sermon' => ['Sermón', 'mic', 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-300'],
    'post' => ['Entrada', 'newspaper', 'bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-300'],
    'page' => ['Página', 'file', 'bg-sky-50 text-sky-600 dark:bg-sky-500/10 dark:text-sky-300'],
];
$hasSermons = (bool) array_filter($menuItems, fn ($i) => $i['slug'] === 'sermones');
$nextActivity = $upcoming[0] ?? null;

// Gráfico de área (SVG)
$w = 320; $h = 150; $pad = 8;
$values = array_column($monthly, 'value');
$max = max(max($values), 1);
$pts = [];
foreach ($values as $i => $v) {
    $pts[] = [$pad + $i * (($w - 2 * $pad) / max(count($values) - 1, 1)), $h - $pad - ($v / $max) * ($h - 2 * $pad - 10)];
}
$line = 'M' . $pts[0][0] . ',' . $pts[0][1];
for ($i = 1; $i < count($pts); $i++) {
    $cx = ($pts[$i - 1][0] + $pts[$i][0]) / 2;
    $line .= " C{$cx},{$pts[$i - 1][1]} {$cx},{$pts[$i][1]} {$pts[$i][0]},{$pts[$i][1]}";
}
$area = $line . " L{$pts[count($pts) - 1][0]},{$h} L{$pts[0][0]},{$h} Z";
$lastPoint = end($pts);
$thisMonth = end($values);
$prevMonth = $values[count($values) - 2] ?? 0;

// Dona de estado
$totalContent = max($stats['published'] + $stats['drafts'] + $stats['scheduled'], 1);
$pct = (int) round($stats['published'] / $totalContent * 100);
$circ = 2 * M_PI * 42;
$segments = [
    ['Publicado', $stats['published'], '#1a939b', 'bg-brand-500'],
    ['Borradores', $stats['drafts'], '#8b5cf6', 'bg-violet-500'],
    ['Programado', $stats['scheduled'], '#9a6b4b', 'bg-earth-500'],
];
?>

<div class="grid gap-6 xl:grid-cols-12">
    <!-- Columna principal -->
    <div class="space-y-6 xl:col-span-8 2xl:col-span-9">

        <!-- Bienvenida + estadísticas -->
        <section class="card overflow-hidden">
            <div class="relative isolate grid gap-6 bg-gradient-to-r from-brand-50 via-white to-earth-50/60 p-6 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 sm:p-8 2xl:grid-cols-[1fr_auto]">
                <?php if ($heroImage): ?>
                    <div class="absolute inset-y-0 left-0 -z-10 hidden w-56 md:block xl:hidden 2xl:block">
                        <img src="<?= $this->e($heroImage) ?>" alt="" class="h-full w-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-brand-50/40 to-brand-50 dark:via-slate-900/60 dark:to-slate-900"></div>
                    </div>
                <?php endif; ?>
                <div class="<?= $heroImage ? 'md:pl-52 xl:pl-0 2xl:pl-52' : '' ?>">
                    <p class="text-lg font-semibold text-gray-500 dark:text-slate-400"><?= $greeting ?>,</p>
                    <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl"><?= $this->e($firstName) ?> <span aria-hidden="true">👋</span></h1>
                    <p class="mt-3 max-w-xl font-serif text-lg italic text-gray-600 dark:text-slate-300">“<?= $this->e($verse['text']) ?>”</p>
                    <p class="mt-1 text-xs font-bold uppercase tracking-widest text-brand-600 dark:text-brand-300"><?= $this->e($verse['ref']) ?> · Versículo del día</p>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row 2xl:flex-col">
                    <div class="flex items-center gap-3 rounded-2xl bg-white/80 px-4 py-3 shadow-soft ring-1 ring-gray-100 backdrop-blur dark:bg-slate-800/80 dark:ring-slate-700">
                        <span class="text-brand-600 dark:text-brand-300"><?= $icon('calendar') ?></span>
                        <span>
                            <span class="block text-sm font-bold text-gray-900 dark:text-white"><?= $longDate($now) ?></span>
                            <span class="block text-xs text-gray-400"><?= $now->format('Y') ?></span>
                        </span>
                    </div>
                    <?php if ($nextActivity && $nextActivity['next']): ?>
                        <div class="rounded-2xl bg-brand-600 px-4 py-3 text-white shadow-lg shadow-brand-600/25">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-brand-100">Próxima reunión</p>
                            <p class="mt-0.5 font-bold"><?= $this->e($nextActivity['title']) ?></p>
                            <p class="text-xs text-brand-100"><?= $longDate($nextActivity['next']) ?> · <?= $this->e($nextActivity['time']) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 p-4 sm:p-6 lg:grid-cols-4 xl:grid-cols-2 2xl:grid-cols-4">
                <?php foreach ([
                    ['Sermones', $stats['sermons'], $stats['sermonsMonth'] . ' este mes', 'mic', 'bg-brand-50 dark:bg-brand-500/10', 'bg-brand-600', '/admin/posts?type=sermon'],
                    ['Entradas', $stats['posts'], $stats['postsMonth'] . ' este mes', 'newspaper', 'bg-violet-50 dark:bg-violet-500/10', 'bg-violet-500', '/admin/posts?type=post'],
                    ['Páginas', $stats['pages'], $stats['published'] . ' publicadas en total', 'file', 'bg-rose-50 dark:bg-rose-500/10', 'bg-rose-500', '/admin/posts?type=page'],
                    ['Archivos', $stats['media'], $stats['mediaMonth'] . ' subidos este mes', 'image', 'bg-earth-50 dark:bg-earth-500/10', 'bg-earth-500', '/admin/media'],
                ] as [$label, $value, $sub, $ic, $bg, $iconBg, $href]): ?>
                    <a href="<?= $href ?>" class="group flex items-start gap-4 rounded-2xl p-4 transition hover:-translate-y-0.5 hover:shadow-md <?= $bg ?>">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl text-white shadow-sm <?= $iconBg ?>"><?= $icon($ic) ?></span>
                        <span>
                            <span class="block text-sm font-semibold text-gray-600 dark:text-slate-300"><?= $label ?></span>
                            <span class="block text-2xl font-extrabold text-gray-900 dark:text-white"><?= (int) $value ?></span>
                            <span class="mt-0.5 flex items-start gap-1 text-xs font-medium text-gray-500 dark:text-slate-400"><?= $icon('arrow-up', 'h-3 w-3') ?> <?= $this->e($sub) ?></span>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Gráfico / Estado / Actividad -->
        <div class="grid gap-6 md:grid-cols-2 2xl:grid-cols-3">
            <section class="card">
                <div class="card-header">
                    <h2 class="card-title"><span class="grid h-7 w-7 place-items-center rounded-lg bg-brand-50 text-brand-600 dark:bg-brand-500/10"><?= $icon('chart', 'h-4 w-4') ?></span> Publicaciones</h2>
                    <span class="badge-gray">Últimos 6 meses</span>
                </div>
                <div class="px-5 pt-3">
                    <p class="text-3xl font-extrabold text-gray-900 dark:text-white"><?= (int) $thisMonth ?> <span class="text-sm font-semibold text-gray-400">este mes</span></p>
                    <?php $delta = $thisMonth - $prevMonth; ?>
                    <p class="text-xs font-semibold <?= $delta >= 0 ? 'text-emerald-600' : 'text-rose-500' ?>"><?= $delta >= 0 ? '↑' : '↓' ?> <?= abs($delta) ?> vs. mes anterior</p>
                </div>
                <div class="px-3 pb-4 pt-2">
                    <svg viewBox="0 0 <?= $w ?> <?= $h + 22 ?>" class="w-full" role="img" aria-label="Contenido publicado por mes">
                        <defs>
                            <linearGradient id="area-fill" x1="0" x2="0" y1="0" y2="1">
                                <stop offset="0%" stop-color="#1a939b" stop-opacity=".28"/>
                                <stop offset="100%" stop-color="#1a939b" stop-opacity="0"/>
                            </linearGradient>
                        </defs>
                        <?php for ($g = 0; $g < 4; $g++): ?>
                            <line x1="<?= $pad ?>" x2="<?= $w - $pad ?>" y1="<?= $pad + $g * ($h - 2 * $pad) / 3 ?>" y2="<?= $pad + $g * ($h - 2 * $pad) / 3 ?>" class="stroke-gray-100 dark:stroke-slate-800" stroke-dasharray="3 4"/>
                        <?php endfor; ?>
                        <path d="<?= $area ?>" fill="url(#area-fill)"/>
                        <path d="<?= $line ?>" fill="none" stroke="#1a939b" stroke-width="2.5" stroke-linecap="round"/>
                        <circle cx="<?= $lastPoint[0] ?>" cy="<?= $lastPoint[1] ?>" r="5" fill="#fff" stroke="#1a939b" stroke-width="2.5"/>
                        <?php foreach ($monthly as $i => $m): ?>
                            <text x="<?= $pts[$i][0] ?>" y="<?= $h + 18 ?>" text-anchor="middle" class="fill-gray-400 text-[10px] font-semibold capitalize"><?= $m['label'] ?></text>
                        <?php endforeach; ?>
                    </svg>
                </div>
            </section>

            <section class="card">
                <div class="card-header">
                    <h2 class="card-title"><span class="grid h-7 w-7 place-items-center rounded-lg bg-violet-50 text-violet-600 dark:bg-violet-500/10"><?= $icon('bolt', 'h-4 w-4') ?></span> Estado del contenido</h2>
                </div>
                <div class="flex items-center gap-6 p-5">
                    <div class="relative h-36 w-36 shrink-0">
                        <svg viewBox="0 0 100 100" class="h-full w-full -rotate-90">
                            <circle cx="50" cy="50" r="42" fill="none" stroke-width="11" class="stroke-gray-100 dark:stroke-slate-800"/>
                            <?php $offset = 0; foreach ($segments as [$label, $value, $color]): $len = $value / $totalContent * $circ; ?>
                                <?php if ($value > 0): ?>
                                    <circle cx="50" cy="50" r="42" fill="none" stroke="<?= $color ?>" stroke-width="11" stroke-linecap="round"
                                            stroke-dasharray="<?= max($len - 2, 0.1) ?> <?= $circ ?>" stroke-dashoffset="<?= -$offset ?>"/>
                                <?php endif; ?>
                            <?php $offset += $len; endforeach; ?>
                        </svg>
                        <div class="absolute inset-0 grid place-items-center text-center">
                            <span><span class="block text-2xl font-extrabold text-gray-900 dark:text-white"><?= $pct ?>%</span><span class="text-[11px] text-gray-400">publicado</span></span>
                        </div>
                    </div>
                    <ul class="space-y-4 text-sm">
                        <?php foreach ($segments as [$label, $value, $color, $dot]): ?>
                            <li class="flex items-start gap-2.5">
                                <span class="mt-1.5 h-2.5 w-2.5 rounded-full <?= $dot ?>"></span>
                                <span><span class="block font-semibold text-gray-700 dark:text-slate-300"><?= $label ?></span><span class="text-xs text-gray-400"><?= (int) $value ?> elementos</span></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </section>

            <section class="card md:col-span-2 2xl:col-span-1">
                <div class="card-header">
                    <h2 class="card-title"><span class="grid h-7 w-7 place-items-center rounded-lg bg-earth-50 text-earth-600 dark:bg-earth-500/10"><?= $icon('users', 'h-4 w-4') ?></span> Actividad reciente</h2>
                    <a href="/admin/posts?type=all" class="card-link">Ver todo <?= $icon('arrow-right', 'h-3 w-3') ?></a>
                </div>
                <div class="flex items-center gap-3 px-5 pt-4">
                    <div class="flex -space-x-2">
                        <?php foreach (array_slice($users, 0, 4) as $i => $u): ?>
                            <span class="grid h-8 w-8 place-items-center rounded-full text-[11px] font-bold text-white ring-2 ring-white dark:ring-slate-900 <?= ['bg-brand-500', 'bg-violet-500', 'bg-earth-500', 'bg-rose-500'][$i % 4] ?>"><?= $this->e(mb_strtoupper(mb_substr($u['name'], 0, 1))) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <span class="text-xs font-semibold text-gray-500"><?= (int) $stats['users'] ?> <?= $stats['users'] === 1 ? 'usuario' : 'usuarios' ?> en el equipo</span>
                </div>
                <ul class="space-y-4 p-5">
                    <?php foreach (array_slice($recent, 0, 4) as $item): ?>
                        <li class="flex gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-gray-100 text-xs font-bold text-gray-600 dark:bg-slate-800 dark:text-slate-300"><?= $this->e(mb_strtoupper(mb_substr($item['author'] ?? '?', 0, 1))) ?></span>
                            <span class="min-w-0 text-sm">
                                <span class="block truncate text-gray-600 dark:text-slate-300">
                                    <strong class="font-semibold text-gray-900 dark:text-white"><?= $this->e(explode(' ', $item['author'] ?? 'Alguien')[0]) ?></strong>
                                    <?= $item['created_at'] === $item['updated_at'] ? 'creó' : 'actualizó' ?>
                                    <a href="/admin/posts/<?= (int) $item['id'] ?>/edit" class="font-semibold text-brand-600 hover:underline dark:text-brand-300"><?= $this->e($item['title']) ?></a>
                                </span>
                                <span class="text-xs text-gray-400"><?= $this->e(time_ago($item['updated_at'])) ?></span>
                            </span>
                        </li>
                    <?php endforeach; ?>
                    <?php if (!$recent): ?>
                        <li class="text-sm text-gray-400">Aún no hay actividad.</li>
                    <?php endif; ?>
                </ul>
            </section>
        </div>

        <!-- Contenido reciente / Accesos rápidos -->
        <div class="grid gap-6 lg:grid-cols-5">
            <section class="card lg:col-span-3">
                <div class="card-header">
                    <h2 class="card-title"><span class="grid h-7 w-7 place-items-center rounded-lg bg-brand-50 text-brand-600 dark:bg-brand-500/10"><?= $icon('folder', 'h-4 w-4') ?></span> Contenido reciente</h2>
                    <a href="/admin/posts?type=all" class="card-link">Ver todo <?= $icon('arrow-right', 'h-3 w-3') ?></a>
                </div>
                <div class="grid gap-4 p-5 sm:grid-cols-2">
                    <?php foreach (array_slice($recent, 0, 4) as $item): ?>
                        <?php [$typeLabel, $typeIcon, $typeColor] = $types[$item['post_type']] ?? [ucfirst($item['post_type']), 'file', 'bg-gray-100 text-gray-600']; ?>
                        <a href="/admin/posts/<?= (int) $item['id'] ?>/edit" class="group rounded-2xl border border-gray-100 p-4 transition hover:border-brand-200 hover:shadow-md dark:border-slate-800 dark:hover:border-brand-500/40">
                            <div class="flex items-start justify-between gap-3">
                                <span class="grid h-11 w-11 place-items-center rounded-xl <?= $typeColor ?>"><?= $icon($typeIcon) ?></span>
                                <span class="<?= $item['status'] === 'published' ? 'badge-green' : 'badge-amber' ?>"><?= $item['status'] === 'published' ? 'Publicado' : 'Borrador' ?></span>
                            </div>
                            <p class="mt-3 truncate font-bold text-gray-900 group-hover:text-brand-600 dark:text-white"><?= $this->e($item['title']) ?></p>
                            <p class="text-xs text-gray-400"><?= $typeLabel ?> · /<?= $this->e($item['slug']) ?></p>
                            <p class="mt-3 flex items-center gap-1.5 text-xs text-gray-400"><?= $icon('clock', 'h-3.5 w-3.5') ?> <?= $this->e(time_ago($item['updated_at'])) ?></p>
                        </a>
                    <?php endforeach; ?>
                    <?php if (!$recent): ?>
                        <p class="col-span-full py-8 text-center text-sm text-gray-400">Crea tu primer contenido con los accesos rápidos.</p>
                    <?php endif; ?>
                </div>
            </section>

            <section class="card lg:col-span-2">
                <div class="card-header">
                    <h2 class="card-title"><span class="grid h-7 w-7 place-items-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10"><?= $icon('bolt', 'h-4 w-4') ?></span> Accesos rápidos</h2>
                </div>
                <div class="grid grid-cols-3 gap-3 p-5">
                    <?php foreach (array_filter([
                        $hasSermons ? ['Nuevo sermón', 'mic', '/admin/posts/new?type=sermon', 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-300'] : null,
                        ['Nueva entrada', 'plus', '/admin/posts/new?type=post', 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-300'],
                        ['Nueva página', 'file', '/admin/posts/new?type=page', 'bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-300'],
                        ['Subir archivo', 'upload', '/admin/media', 'bg-sky-50 text-sky-600 dark:bg-sky-500/10 dark:text-sky-300'],
                        ['Editar menú', 'menu', '/admin/menus', 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-300'],
                        ['Ajustes', 'settings', '/admin/settings', 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-300'],
                    ]) as [$label, $ic, $href, $color]): ?>
                        <a href="<?= $href ?>" class="flex flex-col items-center gap-2 rounded-2xl bg-gray-50 p-3 text-center text-xs font-semibold text-gray-600 transition hover:-translate-y-0.5 hover:bg-white hover:shadow-md dark:bg-slate-800/60 dark:text-slate-300 dark:hover:bg-slate-800">
                            <span class="grid h-11 w-11 place-items-center rounded-xl <?= $color ?>"><?= $icon($ic) ?></span>
                            <?= $label ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    </div>

    <!-- Columna derecha -->
    <div class="space-y-6 xl:col-span-4 2xl:col-span-3">
        <section class="card p-5">
            <div class="flex items-center justify-between">
                <h2 class="card-title"><?= $icon('calendar', 'h-5 w-5 text-brand-600') ?> Calendario</h2>
                <span class="text-sm font-bold text-gray-700 dark:text-slate-300"><?= $this->e($calendar['label']) ?></span>
            </div>
            <div class="mt-4 grid grid-cols-7 gap-y-1 text-center">
                <?php foreach (['Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá', 'Do'] as $d): ?>
                    <span class="pb-2 text-[11px] font-bold uppercase text-gray-400"><?= $d ?></span>
                <?php endforeach; ?>
                <?php foreach ($calendar['weeks'] as $week): ?>
                    <?php foreach ($week as $cell): ?>
                        <span class="flex flex-col items-center gap-0.5 py-1">
                            <span class="grid h-8 w-8 place-items-center rounded-full text-sm
                                <?= $cell['today'] ? 'bg-brand-600 font-bold text-white shadow-md shadow-brand-600/30' : ($cell['inMonth'] ? 'font-semibold text-gray-700 dark:text-slate-300' : 'text-gray-300 dark:text-slate-600') ?>">
                                <?= $cell['day'] ?>
                            </span>
                            <span class="flex h-1.5 gap-0.5">
                                <?php if ($cell['inMonth']): foreach ($cell['dots'] as $dot): ?>
                                    <span class="h-1.5 w-1.5 rounded-full <?= $dotColors[$dot % count($dotColors)] ?>"></span>
                                <?php endforeach; endif; ?>
                            </span>
                        </span>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="card">
            <div class="card-header">
                <h2 class="card-title"><span class="grid h-7 w-7 place-items-center rounded-lg bg-brand-50 text-brand-600 dark:bg-brand-500/10"><?= $icon('clock', 'h-4 w-4') ?></span> Próximas reuniones</h2>
                <a href="/admin/settings" class="card-link">Editar <?= $icon('arrow-right', 'h-3 w-3') ?></a>
            </div>
            <ul class="divide-y divide-gray-100 px-5 py-2 dark:divide-slate-800">
                <?php foreach ($upcoming as $activity): ?>
                    <?php
                    $diffDays = (int) $now->setTime(0, 0)->diff($activity['next']->setTime(0, 0))->days;
                    $when = $diffDays === 0 ? 'Hoy' : ($diffDays === 1 ? 'Mañana' : $longDate($activity['next']));
                    ?>
                    <li class="flex items-center gap-3 py-3">
                        <span class="h-9 w-1.5 shrink-0 rounded-full <?= $dotColors[$activity['index'] % count($dotColors)] ?>"></span>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-bold text-gray-900 dark:text-white"><?= $this->e($activity['title']) ?></span>
                            <span class="text-xs text-gray-400"><?= $this->e($when) ?> · <?= $this->e($activity['time']) ?></span>
                        </span>
                        <span class="<?= $diffDays === 0 ? 'badge-green' : ($diffDays <= 2 ? 'badge-amber' : 'badge-gray') ?>"><?= $diffDays === 0 ? 'Hoy' : 'En ' . $diffDays . ' d' ?></span>
                    </li>
                <?php endforeach; ?>
                <?php if (!$upcoming): ?>
                    <li class="py-4 text-sm text-gray-400">Agrega horarios en Ajustes con el formato <code>Título | Horario | Día</code>.</li>
                <?php endif; ?>
            </ul>
        </section>

        <section class="relative isolate overflow-hidden rounded-2xl bg-gradient-to-br from-brand-800 via-brand-900 to-brand-950 p-6 text-white shadow-soft">
            <svg class="absolute -right-6 -top-6 -z-10 h-40 w-40 text-white/5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path d="M12 3v18M6 9h12"/></svg>
            <div class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-white/10"><?= $icon('globe') ?></span>
                <span>
                    <span class="block font-bold">Tu sitio web</span>
                    <span class="block text-xs text-brand-200">Publicado y en línea</span>
                </span>
                <span class="relative ml-auto flex h-2.5 w-2.5"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-400"></span></span>
            </div>
            <p class="mt-5 rounded-xl bg-white/10 px-4 py-3 text-sm text-brand-50">¿Qué quieres compartir hoy con la congregación?</p>
            <div class="mt-3 grid grid-cols-2 gap-2">
                <a href="/" target="_blank" class="btn bg-white text-brand-800 hover:bg-brand-50"><?= $icon('external', 'h-4 w-4') ?> Ver sitio</a>
                <a href="<?= $hasSermons ? '/admin/posts/new?type=sermon' : '/admin/posts/new?type=post' ?>" class="btn bg-white/10 text-white hover:bg-white/20"><?= $icon('plus', 'h-4 w-4') ?> Publicar</a>
            </div>
        </section>
    </div>

    <!-- Fila inferior -->
    <div class="grid gap-6 md:grid-cols-2 xl:col-span-12 2xl:grid-cols-4">
        <section class="card p-5">
            <h2 class="card-title"><?= $icon('clock', 'h-5 w-5 text-brand-600') ?> Horario semanal</h2>
            <p class="mt-3 text-3xl font-extrabold text-gray-900 dark:text-white"><?= count($upcoming) ?> <span class="text-sm font-semibold text-gray-400">reuniones por semana</span></p>
            <ul class="mt-4 space-y-2.5 text-sm">
                <?php foreach (service_schedule() as $i => $item): ?>
                    <li class="flex items-center gap-2.5">
                        <span class="h-2 w-2 shrink-0 rounded-full <?= $dotColors[$i % count($dotColors)] ?>"></span>
                        <span class="flex-1 truncate font-medium text-gray-700 dark:text-slate-300"><?= $this->e($item['title']) ?></span>
                        <span class="shrink-0 text-xs text-gray-400"><?= $this->e($item['time']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>

        <section class="card p-5">
            <div class="flex items-center justify-between">
                <h2 class="card-title"><?= $icon('users', 'h-5 w-5 text-violet-500') ?> Equipo</h2>
                <?php if ($currentUser->can('manage_users')): ?><a href="/admin/users" class="card-link">Gestionar <?= $icon('arrow-right', 'h-3 w-3') ?></a><?php endif; ?>
            </div>
            <ul class="mt-4 space-y-3">
                <?php foreach ($users as $i => $u): ?>
                    <li class="flex items-center gap-3">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full text-xs font-bold text-white <?= ['bg-brand-500', 'bg-violet-500', 'bg-earth-500', 'bg-rose-500'][$i % 4] ?>"><?= $this->e(mb_strtoupper(mb_substr($u['name'], 0, 1))) ?></span>
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-semibold text-gray-900 dark:text-white"><?= $this->e($u['name']) ?></span>
                            <span class="block truncate text-xs text-gray-400"><?= $this->e($u['email']) ?></span>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>

        <section class="card p-5">
            <div class="flex items-center justify-between">
                <h2 class="card-title"><?= $icon('image', 'h-5 w-5 text-earth-500') ?> Archivos recientes</h2>
                <a href="/admin/media" class="card-link">Ver todo <?= $icon('arrow-right', 'h-3 w-3') ?></a>
            </div>
            <ul class="mt-4 space-y-3">
                <?php foreach ($recentMedia as $file): ?>
                    <li class="flex items-center gap-3">
                        <?php if (str_starts_with($file['mime_type'], 'image/')): ?>
                            <img src="/<?= $this->e($file['file_path']) ?>" alt="" class="h-10 w-10 shrink-0 rounded-lg object-cover">
                        <?php else: ?>
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-red-50 text-red-500"><?= $icon('file') ?></span>
                        <?php endif; ?>
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-semibold text-gray-900 dark:text-white"><?= $this->e($file['file_name']) ?></span>
                            <span class="text-xs text-gray-400"><?= number_format($file['size'] / 1024 / 1024, 1) ?> MB · <?= $this->e(time_ago($file['created_at'])) ?></span>
                        </span>
                    </li>
                <?php endforeach; ?>
                <?php if (!$recentMedia): ?>
                    <li class="text-sm text-gray-400">Aún no hay archivos.</li>
                <?php endif; ?>
            </ul>
        </section>

        <section class="relative isolate flex min-h-[220px] flex-col justify-end overflow-hidden rounded-2xl p-6 text-white shadow-soft">
            <?php $verseImage = option_array('welcome_images')[2] ?? $heroImage; ?>
            <?php if ($verseImage): ?>
                <img src="<?= $this->e($verseImage) ?>" alt="" class="absolute inset-0 -z-20 h-full w-full object-cover">
            <?php endif; ?>
            <div class="absolute inset-0 -z-10 bg-gradient-to-t from-earth-700/95 via-earth-700/50 to-transparent"></div>
            <p class="font-serif text-2xl italic leading-snug">“Lámpara es a mis pies tu palabra, y lumbrera a mi camino.”</p>
            <p class="mt-2 text-xs font-bold uppercase tracking-widest text-earth-100">Salmo 119:105</p>
        </section>
    </div>
</div>
