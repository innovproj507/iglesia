<?php
$siteTitle = option('site_title', 'Iglesia CMS');
$logo = option('logo');
$image = option_array('welcome_images')[0] ?? option('hero_image');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión · <?= $this->e($siteTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@1,500&display=swap" rel="stylesheet">
    <script>try{var t=localStorage.getItem('admin-theme');if(t==='dark'||(!t&&matchMedia('(prefers-color-scheme: dark)').matches))document.documentElement.classList.add('dark')}catch(e){}</script>
    <link rel="stylesheet" href="/assets/admin/app.css">
</head>
<body class="font-sans">
<div class="grid min-h-screen lg:grid-cols-2">
    <div class="relative isolate hidden overflow-hidden bg-brand-900 lg:block">
        <?php if ($image): ?>
            <img src="<?= $this->e($image) ?>" alt="" class="absolute inset-0 -z-20 h-full w-full object-cover">
        <?php endif; ?>
        <div class="absolute inset-0 -z-10 bg-gradient-to-t from-brand-950 via-brand-950/60 to-brand-900/30"></div>
        <div class="flex h-full flex-col justify-end p-12 text-white">
            <p class="max-w-md font-serif text-3xl italic leading-snug">“Mas buscad primeramente el reino de Dios y su justicia, y todas estas cosas os serán añadidas.”</p>
            <p class="mt-4 text-xs font-bold uppercase tracking-[0.25em] text-brand-200">Mateo 6:33</p>
        </div>
    </div>

    <div class="flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-sm">
            <?php if ($logo): ?>
                <img src="<?= $this->e($logo) ?>" alt="<?= $this->e($siteTitle) ?>" class="mb-10 h-14 w-auto dark:brightness-0 dark:invert">
            <?php else: ?>
                <p class="mb-10 text-xl font-extrabold text-brand-800 dark:text-white"><?= $this->e($siteTitle) ?></p>
            <?php endif; ?>

            <h1 class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">Bienvenido de nuevo</h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-slate-400">Ingresa para administrar el sitio de la iglesia.</p>

            <?php if ($error): ?>
                <div class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-300">
                    <?= $this->e($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="/admin/login" class="mt-8 space-y-5">
                <?= csrf_field() ?>
                <div>
                    <label class="label" for="email">Correo</label>
                    <input id="email" type="email" name="email" required autofocus autocomplete="username" class="input py-3">
                </div>
                <div>
                    <label class="label" for="password">Contraseña</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password" class="input py-3">
                </div>
                <button type="submit" class="btn-primary w-full py-3">Ingresar</button>
            </form>

            <a href="/" class="mt-8 inline-block text-sm font-semibold text-gray-400 hover:text-brand-600">← Volver al sitio</a>
        </div>
    </div>
</div>
</body>
</html>
