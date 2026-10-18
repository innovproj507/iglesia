<?php
$schedule = function_exists('service_schedule') ? service_schedule() : [];
$whatsapp = function_exists('whatsapp_url') ? whatsapp_url() : null;
$phone = option('phone');
$email = option('email');
$address = option('address');
$hasPartial = fn (string $name) => $this->engine->exists($name);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title><?= $this->e($title) ?> · <?= $this->e($siteTitle) ?></title>
    <meta name="theme-color" content="#0a2729">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;1,500&family=Raleway:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/theme/app.css">
</head>
<body class="bg-brand-950 font-sans text-white antialiased">
<main class="relative isolate flex min-h-screen flex-col overflow-hidden">
    <?php if ($image): ?>
        <img src="<?= $this->e($image) ?>" alt="" class="absolute inset-0 -z-20 h-full w-full object-cover animate-slow-zoom">
    <?php endif; ?>
    <div class="absolute inset-0 -z-10 bg-gradient-to-b from-brand-950/85 via-brand-900/80 to-brand-950/95"></div>
    <svg class="absolute inset-0 -z-10 h-full w-full opacity-[0.05]" aria-hidden="true">
        <defs><pattern id="m-cross" width="56" height="56" patternUnits="userSpaceOnUse"><path d="M28 16v24M20 24h16" stroke="white" stroke-width="2" fill="none"/></pattern></defs>
        <rect width="100%" height="100%" fill="url(#m-cross)"/>
    </svg>

    <header class="container-site flex justify-center pt-10 sm:justify-start">
        <?php if ($logo): ?>
            <img src="<?= $this->e($logo) ?>" alt="<?= $this->e($siteTitle) ?>" class="h-12 w-auto brightness-0 invert sm:h-14">
        <?php else: ?>
            <span class="text-xl font-extrabold"><?= $this->e($siteTitle) ?></span>
        <?php endif; ?>
    </header>

    <section class="container-site flex flex-1 flex-col items-center justify-center py-16 text-center">
        <span class="animate-fade-up mb-8 grid h-20 w-20 place-items-center rounded-3xl border border-white/15 bg-white/10 backdrop-blur">
            <svg class="h-10 w-10 text-brand-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.65 2.65 0 0 0 21 17.25l-5.88-5.88M11.42 15.17l2.5-3.03c.31-.38.73-.63 1.2-.76.55-.16 1.16-.13 1.73-.08a4.5 4.5 0 0 0 4.49-6.3l-3.27 3.28a3 3 0 0 1-2.25-2.25l3.28-3.27a4.5 4.5 0 0 0-6.3 4.49c.09 1.02-.06 2.15-.85 2.8l-.09.08m-1.44 4.94-4.13 5.01a2.65 2.65 0 0 1-3.75-3.75l5.01-4.13m2.87 2.87-2.87-2.87M4.87 4.87 2.25 2.25m2.62 2.62L7.5 7.5m-2.63-2.63L3 6.75l1.5 1.5 1.88-1.88"/></svg>
        </span>

        <h1 class="animate-fade-up [animation-delay:150ms] max-w-3xl text-4xl font-extrabold leading-tight tracking-tight sm:text-6xl"><?= $this->e($title) ?></h1>
        <p class="animate-fade-up [animation-delay:300ms] mt-6 max-w-xl text-lg text-brand-50/90 sm:text-xl"><?= nl2br($this->e($message)) ?></p>

        <?php if ($until): ?>
            <div class="animate-fade-up [animation-delay:450ms] mt-12" id="countdown" data-until="<?= $this->e($until) ?>">
                <p class="mb-4 text-xs font-bold uppercase tracking-[0.25em] text-brand-300">Volvemos en</p>
                <div class="flex justify-center gap-3 sm:gap-4">
                    <?php foreach (['d' => 'días', 'h' => 'horas', 'm' => 'min', 's' => 'seg'] as $key => $label): ?>
                        <div class="w-20 rounded-2xl border border-white/10 bg-white/10 py-4 backdrop-blur sm:w-24">
                            <span data-unit="<?= $key ?>" class="block text-3xl font-extrabold lining-nums tabular-nums sm:text-4xl">--</span>
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-brand-200"><?= $label ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($showContact): ?>
            <?php if ($schedule): ?>
                <div class="animate-fade-up [animation-delay:600ms] mt-14 max-w-3xl">
                    <p class="mb-4 text-sm text-brand-100/80">Mientras tanto, ¡te esperamos en persona!</p>
                    <ul class="flex flex-wrap justify-center gap-3">
                        <?php foreach ($schedule as $item): ?>
                            <li class="rounded-xl border border-white/10 bg-white/10 px-4 py-2.5 text-sm backdrop-blur">
                                <strong class="font-semibold"><?= $this->e($item['title']) ?></strong>
                                <?php if ($item['time']): ?><span class="text-brand-100/80"> · <?= $this->e($item['time']) ?></span><?php endif; ?>
                                <?php if ($item['day']): ?><span class="block text-xs text-brand-200/70"><?= $this->e($item['day']) ?></span><?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="animate-fade-up [animation-delay:700ms] mt-10 flex flex-wrap justify-center gap-3">
                <?php if ($whatsapp): ?>
                    <a href="<?= $this->e($whatsapp) ?>" target="_blank" rel="noopener" class="btn bg-[#25D366] text-white hover:brightness-95">
                        <?php if ($hasPartial('partials/whatsapp-icon')) $this->insert('partials/whatsapp-icon', ['class' => 'h-5 w-5']) ?> WhatsApp
                    </a>
                <?php endif; ?>
                <?php if ($phone): ?>
                    <a href="tel:<?= $this->e(preg_replace('/[^\d+]/', '', $phone)) ?>" class="btn-outline"><?= $this->e($phone) ?></a>
                <?php endif; ?>
                <?php if ($email): ?>
                    <a href="mailto:<?= $this->e($email) ?>" class="btn-outline"><?= $this->e($email) ?></a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </section>

    <footer class="container-site flex flex-col items-center justify-between gap-4 border-t border-white/10 py-6 text-xs text-brand-100/60 sm:flex-row">
        <p><?= $address && $showContact ? $this->e($address) : '&copy; ' . date('Y') . ' ' . $this->e($siteTitle) ?></p>
        <?php if ($showContact && $hasPartial('partials/social-icons')): ?>
            <div class="flex gap-4"><?php $this->insert('partials/social-icons', ['socialLinks' => $socialLinks, 'size' => 'h-5 w-5', 'class' => 'text-white/60 hover:text-white']) ?></div>
        <?php endif; ?>
    </footer>
</main>

<?php if ($until): ?>
<script>
(function () {
    var el = document.getElementById('countdown');
    var end = new Date(el.dataset.until).getTime();
    var units = { d: 86400, h: 3600, m: 60, s: 1 };
    function tick() {
        var left = Math.max(0, Math.floor((end - Date.now()) / 1000));
        Object.keys(units).forEach(function (k) {
            var v = Math.floor(left / units[k]);
            left -= v * units[k];
            el.querySelector('[data-unit="' + k + '"]').textContent = String(v).padStart(2, '0');
        });
        if (end - Date.now() <= 0) { clearInterval(timer); setTimeout(function () { location.reload(); }, 5000); }
    }
    var timer = setInterval(tick, 1000);
    tick();
})();
</script>
<?php endif; ?>
</body>
</html>
