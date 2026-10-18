<?php $this->insert('partials/header', get_defined_vars()) ?>
<?php $this->insert('partials/hero', get_defined_vars()) ?>

<?php
$welcomeText = option('welcome_text') ?: "Somos una familia de fe que cree que Dios transforma vidas a través de Su Palabra y de una comunidad que se cuida mutuamente.\n\nNo importa de dónde vengas ni en qué etapa estés: aquí encontrarás un lugar para crecer y descubrir el propósito que Dios tiene para ti.";
$welcomeImages = option_array('welcome_images');
$mission = option('mission');
$missionQuote = option('mission_quote');
$pillars = option_array('pillars');
$pillarsQuote = option_array('pillars_quote');
$nextSteps = option_array('next_steps');
$instagram = ($socialLinks ?? [])['instagram'] ?? null;
$featured = $recentSermons[0] ?? null;
$moreSermons = array_slice($recentSermons ?? [], 1, 3);
$icons = [
    'M9.81 15.9 9 18.75l-.81-2.84a4.5 4.5 0 0 0-3.09-3.1L2.25 12l2.84-.81a4.5 4.5 0 0 0 3.1-3.09L9 5.25l.81 2.84a4.5 4.5 0 0 0 3.09 3.1l2.85.81-2.84.81a4.5 4.5 0 0 0-3.1 3.09ZM18.26 8.72 18 9.75l-.26-1.03a3.38 3.38 0 0 0-2.46-2.46L14.25 6l1.03-.26a3.38 3.38 0 0 0 2.46-2.46L18 2.25l.26 1.03a3.38 3.38 0 0 0 2.46 2.46l1.03.26-1.03.26a3.38 3.38 0 0 0-2.46 2.46Z',
    'M9 12.75 11.25 15 15 9.75m-3-7.04A11.96 11.96 0 0 1 3.6 6 12 12 0 0 0 3 9.75c0 5.59 3.82 10.29 9 11.62 5.18-1.33 9-6.03 9-11.62 0-1.31-.21-2.57-.6-3.75h-.15c-3.2 0-6.1-1.25-8.25-3.29Z',
    'M21 8.25c0-2.49-2.1-4.5-4.69-4.5-1.93 0-3.6 1.13-4.31 2.73-.72-1.6-2.38-2.73-4.31-2.73C5.1 3.75 3 5.76 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z',
    'M10.05 4.58c0-.86.7-1.58 1.58-1.58h.74c.87 0 1.58.72 1.58 1.58v.01c0 .87.7 1.58 1.58 1.58H17a2 2 0 0 1 2 2v.53a2 2 0 0 1-.59 1.42l-5 5a2 2 0 0 1-2.82 0l-5-5A2 2 0 0 1 5 8.7v-.53a2 2 0 0 1 2-2h1.47c.87 0 1.58-.71 1.58-1.58ZM12 15.75V21',
    'M10.34 15.84c-.69-.03-1.38-.04-2.09-.04H7.5a4.5 4.5 0 1 1 0-9h.75c.7 0 1.4-.01 2.09-.04m0 9.08c.25.97.58 1.9.98 2.8.25.56.06 1.22-.47 1.52l-.66.38a1.13 1.13 0 0 1-1.5-.38 18.89 18.89 0 0 1-1.85-4.24m3.5.03c2.7.11 5.3.7 7.7 1.69a.75.75 0 0 0 1.01-.65 22.55 22.55 0 0 0 0-10.5.75.75 0 0 0-1.02-.65c-2.38.98-4.99 1.57-7.69 1.68m0 9.08a18.02 18.02 0 0 1 0-9.08',
];
?>

<!-- Bienvenida -->
<section id="bienvenida" class="relative scroll-mt-24 py-24 sm:py-28">
    <div class="container-site grid items-center gap-16 lg:grid-cols-2">
        <div class="reveal">
            <span class="eyebrow">Bienvenidos</span>
            <h2 class="section-title mt-3">Un lugar donde puedes <span class="font-serif italic font-medium text-brand-600">encontrarte con Jesús</span></h2>
            <div class="mt-6 space-y-4 text-lg leading-relaxed text-gray-600">
                <?php foreach (preg_split('/\R{2,}/', trim($welcomeText)) as $paragraph): ?>
                    <p><?= nl2br($this->e($paragraph)) ?></p>
                <?php endforeach; ?>
            </div>
            <div class="mt-8 flex flex-wrap gap-4">
                <a href="#horarios" class="btn-primary">Ver horarios</a>
                <a href="/conocenos" class="btn-ghost">Conócenos</a>
            </div>
        </div>

        <?php if (count($welcomeImages) >= 2): ?>
            <div class="reveal relative grid grid-cols-2 gap-4">
                <img src="<?= $this->e($welcomeImages[0]) ?>" alt="" loading="lazy" class="col-span-2 aspect-[16/9] w-full rounded-3xl object-cover shadow-xl">
                <img src="<?= $this->e($welcomeImages[1]) ?>" alt="" loading="lazy" class="aspect-[4/3] w-full rounded-3xl object-cover shadow-lg">
                <?php if (!empty($welcomeImages[2])): ?>
                    <img src="<?= $this->e($welcomeImages[2]) ?>" alt="" loading="lazy" class="aspect-[4/3] w-full rounded-3xl object-cover shadow-lg">
                <?php else: ?>
                    <div class="grid aspect-[4/3] place-items-center rounded-3xl bg-earth-700 p-4 text-center text-earth-50">
                        <p class="font-serif text-lg italic">Una familia que ama</p>
                    </div>
                <?php endif; ?>
                <div class="absolute -left-4 top-6 hidden rounded-2xl bg-white px-5 py-4 shadow-xl sm:block">
                    <p class="text-xs font-bold uppercase tracking-widest text-brand-500">Domingos</p>
                    <p class="text-lg font-extrabold text-gray-900">¡Te esperamos!</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php if ($mission): ?>
<!-- Misión -->
<section class="relative isolate overflow-hidden bg-earth-700 py-20 text-earth-50">
    <svg class="absolute inset-0 -z-10 h-full w-full opacity-[0.05]" aria-hidden="true">
        <defs><pattern id="mission-cross" width="56" height="56" patternUnits="userSpaceOnUse"><path d="M28 16v24M20 24h16" stroke="white" stroke-width="2" fill="none"/></pattern></defs>
        <rect width="100%" height="100%" fill="url(#mission-cross)"/>
    </svg>
    <div class="container-site reveal mx-auto max-w-4xl text-center">
        <span class="eyebrow text-earth-200">Nuestra misión</span>
        <p class="mt-6 text-2xl font-semibold leading-relaxed text-white sm:text-3xl"><?= $this->e($mission) ?></p>
        <?php if ($missionQuote): ?>
            <p class="mt-8 font-serif text-xl italic text-earth-200">“<?= $this->e($missionQuote) ?>”</p>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php if ($pillars): ?>
<!-- Pilares -->
<section class="py-24">
    <div class="container-site">
        <div class="reveal mx-auto mb-14 max-w-2xl text-center">
            <span class="eyebrow">Lo que nos define</span>
            <h2 class="section-title mt-3">Quiénes somos</h2>
        </div>
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-6">
            <?php foreach ($pillars as $i => $pillar): ?>
                <article class="reveal group relative flex flex-col rounded-3xl bg-white p-8 shadow-sm ring-1 ring-gray-100 transition hover:-translate-y-1 hover:shadow-xl lg:col-span-2 <?= ($i === 3 && count($pillars) === 5) ? 'lg:col-start-2' : '' ?>" style="transition-delay: <?= ($i % 3) * 100 ?>ms">
                    <span class="mb-6 grid h-14 w-14 place-items-center rounded-2xl bg-brand-50 text-brand-600 transition group-hover:bg-brand-500 group-hover:text-white">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="<?= $icons[$i % count($icons)] ?>"/></svg>
                    </span>
                    <h3 class="text-xl font-bold text-gray-900"><?= $this->e($pillar['title'] ?? '') ?></h3>
                    <p class="mt-3 flex-1 leading-relaxed text-gray-600"><?= $this->e($pillar['text'] ?? '') ?></p>
                    <?php if (!empty($pillar['ref'])): ?>
                        <p class="mt-6 inline-flex items-center gap-2 text-sm font-bold text-earth-600">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.04A8.97 8.97 0 0 0 6 3.75c-1.05 0-2.06.18-3 .51v14.25A8.99 8.99 0 0 1 6 18c2.3 0 4.4.87 6 2.29m0-14.25a8.97 8.97 0 0 1 6-2.29c1.05 0 2.06.18 3 .51v14.25A8.99 8.99 0 0 0 18 18a8.97 8.97 0 0 0-6 2.29m0-14.25v14.25"/></svg>
                            <?= $this->e($pillar['ref']) ?>
                        </p>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
        <?php if (!empty($pillarsQuote['text'])): ?>
            <figure class="reveal mx-auto mt-16 max-w-3xl text-center">
                <blockquote class="font-serif text-3xl italic text-brand-700 sm:text-4xl">“<?= $this->e($pillarsQuote['text']) ?>”</blockquote>
                <figcaption class="mt-4 text-sm font-bold uppercase tracking-[0.25em] text-gray-400"><?= $this->e($pillarsQuote['ref'] ?? '') ?></figcaption>
            </figure>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php if ($nextSteps): ?>
<!-- Próximos pasos -->
<section class="bg-gradient-to-b from-brand-50/70 to-white py-24">
    <div class="container-site">
        <div class="reveal mb-12 text-center">
            <span class="eyebrow">Da tu siguiente paso</span>
            <h2 class="section-title mt-3">Crece con nosotros</h2>
        </div>
        <div class="grid gap-8 md:grid-cols-3">
            <?php foreach ($nextSteps as $i => $step): ?>
                <?php $external = preg_match('~^https?://~', $step['url'] ?? ''); ?>
                <a href="<?= $this->e($step['url'] ?? '#') ?>" <?= $external ? 'target="_blank" rel="noopener"' : '' ?>
                   class="reveal group relative isolate flex aspect-[4/5] flex-col justify-end overflow-hidden rounded-3xl p-8 text-white shadow-lg" style="transition-delay: <?= $i * 100 ?>ms">
                    <img src="<?= $this->e($step['image'] ?? '') ?>" alt="" loading="lazy" class="absolute inset-0 -z-20 h-full w-full object-cover transition duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 -z-10 bg-gradient-to-t from-brand-950/95 via-brand-950/40 to-transparent"></div>
                    <h3 class="text-2xl font-bold"><?= $this->e($step['title'] ?? '') ?></h3>
                    <p class="mt-2 text-sm leading-relaxed text-white/80"><?= $this->e($step['text'] ?? '') ?></p>
                    <span class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-brand-200">
                        <?= $this->e($step['cta'] ?? 'Saber más') ?> <span class="transition group-hover:translate-x-1">→</span>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($featured): ?>
<!-- Sermón destacado -->
<section class="py-24">
    <div class="container-site">
        <div class="reveal mb-12 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <span class="eyebrow">Lo más reciente</span>
                <h2 class="section-title mt-3">Mensaje de la semana</h2>
            </div>
            <a href="/sermones" class="btn-ghost">Todos los sermones</a>
        </div>
        <?php
        $featuredImage = post_image($featured);
        $featuredExcerpt = $featured['excerpt'] ?: mb_strimwidth(trim(strip_tags($featured['content'] ?? '')), 0, 220, '…');
        $preacher = $featured['predicador'] ?? '';
        $initials = $preacher ? mb_strtoupper(implode('', array_map(fn ($w) => mb_substr($w, 0, 1), array_slice(preg_split('/\s+/', $preacher), 0, 2)))) : '';
        ?>
        <div class="grid gap-8 lg:grid-cols-3">
            <article class="reveal overflow-hidden rounded-3xl bg-white shadow-xl ring-1 ring-gray-100 lg:col-span-2">
                <a href="/sermones/<?= $this->e($featured['slug']) ?>" class="group relative block aspect-video overflow-hidden bg-brand-800">
                    <?php if ($featuredImage): ?>
                        <img src="<?= $this->e($featuredImage) ?>" alt="" class="h-full w-full object-cover opacity-90 transition duration-700 group-hover:scale-105">
                    <?php else: ?>
                        <div class="absolute inset-0 bg-gradient-to-br from-brand-500 to-brand-900"></div>
                    <?php endif; ?>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent"></div>
                    <span class="absolute left-1/2 top-1/2 grid h-20 w-20 -translate-x-1/2 -translate-y-1/2 place-items-center rounded-full bg-white/95 text-brand-600 shadow-2xl transition group-hover:scale-110">
                        <svg class="ml-1 h-8 w-8" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5.14v13.72a1 1 0 0 0 1.5.86l11.04-6.86a1 1 0 0 0 0-1.72L9.5 4.28A1 1 0 0 0 8 5.14Z"/></svg>
                    </span>
                    <span class="absolute bottom-4 left-5 rounded-full bg-white/95 px-3 py-1 text-xs font-bold text-brand-700">
                        <?= $this->e(format_date($featured['fecha_servicio'] ?? $featured['published_at'] ?? '')) ?>
                    </span>
                </a>
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-gray-900 sm:text-3xl"><?= $this->e($featured['title']) ?></h3>
                    <?php if ($featuredExcerpt): ?>
                        <blockquote class="mt-5 border-l-4 border-earth-400 pl-5 font-serif text-lg italic text-gray-600">“<?= $this->e($featuredExcerpt) ?>”</blockquote>
                    <?php endif; ?>
                    <div class="mt-8 flex flex-wrap items-center justify-between gap-6">
                        <?php if ($preacher): ?>
                            <div class="flex items-center gap-3">
                                <span class="grid h-12 w-12 place-items-center rounded-full bg-earth-100 font-bold text-earth-700"><?= $this->e($initials) ?></span>
                                <span>
                                    <span class="block font-bold text-gray-900"><?= $this->e($preacher) ?></span>
                                    <span class="block text-xs text-gray-500">Predicador</span>
                                </span>
                            </div>
                        <?php endif; ?>
                        <a href="/sermones/<?= $this->e($featured['slug']) ?>" class="btn-primary">Ver ahora</a>
                    </div>
                </div>
            </article>
            <div class="flex flex-col gap-4">
                <?php foreach ($moreSermons as $sermon): ?>
                    <?php $thumb = post_image($sermon); ?>
                    <a href="/sermones/<?= $this->e($sermon['slug']) ?>" class="reveal group flex gap-4 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-100 transition hover:shadow-lg">
                        <span class="relative h-20 w-28 shrink-0 overflow-hidden rounded-xl bg-gradient-to-br from-brand-400 to-brand-700">
                            <?php if ($thumb): ?><img src="<?= $this->e($thumb) ?>" alt="" loading="lazy" class="h-full w-full object-cover"><?php endif; ?>
                        </span>
                        <span class="min-w-0">
                            <span class="block text-xs text-gray-400"><?= $this->e(format_date($sermon['fecha_servicio'] ?? $sermon['published_at'] ?? '')) ?></span>
                            <span class="mt-1 block font-bold leading-snug text-gray-900 line-clamp-2 group-hover:text-brand-600"><?= $this->e($sermon['title']) ?></span>
                            <?php if (!empty($sermon['predicador'])): ?>
                                <span class="mt-1 block text-xs text-gray-500"><?= $this->e($sermon['predicador']) ?></span>
                            <?php endif; ?>
                        </span>
                    </a>
                <?php endforeach; ?>
                <div class="reveal mt-auto rounded-2xl bg-earth-700 p-6 text-earth-50">
                    <p class="font-serif text-lg italic leading-relaxed">“Así que la fe es por el oír, y el oír, por la palabra de Dios.”</p>
                    <p class="mt-3 text-xs font-bold uppercase tracking-widest text-earth-200">Romanos 10:17</p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($recentPosts)): ?>
<!-- Noticias -->
<section class="py-24">
    <div class="container-site">
        <div class="reveal mb-12 text-center">
            <span class="eyebrow">Noticias</span>
            <h2 class="section-title mt-3">Lo último en nuestra iglesia</h2>
        </div>
        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($recentPosts as $post): ?>
                <?= $this->fetch('partials/card-post', ['post' => $post, 'href' => '/' . $post['slug']]) ?>
            <?php endforeach; ?>
        </div>
        <div class="mt-12 text-center">
            <a href="/blog" class="btn-ghost">Ver todas las noticias</a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php $this->insert('partials/service-times', get_defined_vars()) ?>

<?php if ($instagram): ?>
<!-- Redes -->
<section class="py-24">
    <div class="container-site">
        <div class="reveal relative isolate overflow-hidden rounded-[2rem] bg-gradient-to-br from-brand-500 via-brand-700 to-earth-700 px-8 py-16 text-center text-white shadow-2xl sm:px-16">
            <div class="absolute -right-20 -top-20 -z-10 h-72 w-72 rounded-full bg-white/10 blur-2xl"></div>
            <span class="mx-auto mb-6 grid h-16 w-16 place-items-center rounded-2xl bg-white/15 backdrop-blur">
                <?php $this->insert('partials/social-icons', ['socialLinks' => ['instagram' => $instagram], 'size' => 'h-8 w-8', 'class' => 'text-white']) ?>
            </span>
            <h2 class="text-3xl font-bold sm:text-4xl">Mantente conectado</h2>
            <p class="mx-auto mt-4 max-w-2xl text-lg text-white/85">
                Compartimos momentos de adoración, reflexiones diarias y actualizaciones de nuestros eventos.
                Síguenos en <strong>@<?= $this->e(trim((string) parse_url($instagram, PHP_URL_PATH), '/')) ?></strong>.
            </p>
            <a href="<?= $this->e($instagram) ?>" target="_blank" rel="noopener" class="btn mt-8 bg-white text-brand-800 hover:bg-brand-50">Síguenos en Instagram</a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php $this->insert('partials/footer', get_defined_vars()) ?>
