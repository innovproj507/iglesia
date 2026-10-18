<?php
$slides = option_array('hero_slides');
$heroImage = option('hero_image');
$liveUrl = option('live_url') ?: (($socialLinks ?? [])['youtube'] ?? null);
$mapsUrl = maps_url();
?>
<?php if ($slides): ?>
<section class="relative isolate overflow-hidden bg-brand-950 text-white"
         x-data="{
            current: 0,
            total: <?= count($slides) ?>,
            timer: null,
            paused: false,
            go(i) { this.current = (i + this.total) % this.total },
            start() {
                if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
                this.timer = setInterval(() => { if (!this.paused) this.go(this.current + 1) }, 7000);
            }
         }"
         x-init="start()" @mouseenter="paused = true" @mouseleave="paused = false"
         @keydown.left="go(current - 1)" @keydown.right="go(current + 1)"
         aria-roledescription="carrusel" aria-label="Versículos">

    <?php foreach ($slides as $i => $slide): ?>
        <div class="absolute inset-0 -z-20 transition-opacity duration-[1500ms]"
             :class="current === <?= $i ?> ? 'opacity-100' : 'opacity-0'" <?= $i ? 'x-cloak' : '' ?>>
            <img src="<?= $this->e($slide['image'] ?? $heroImage) ?>" alt="" <?= $i ? 'loading="lazy"' : 'fetchpriority="high"' ?>
                 class="h-full w-full object-cover transition-transform duration-[8000ms] ease-out"
                 :class="current === <?= $i ?> ? 'scale-100' : 'scale-110'">
        </div>
    <?php endforeach; ?>
    <div class="absolute inset-0 -z-10 bg-gradient-to-b from-brand-950/80 via-brand-950/60 to-brand-950/90"></div>

    <div class="container-site relative flex min-h-[min(86vh,880px)] flex-col items-center justify-center py-28 text-center">
        <span class="mb-8 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.2em] backdrop-blur">
            <span class="h-1.5 w-1.5 rounded-full bg-brand-300"></span> <?= $this->e($siteTitle ?? '') ?>
        </span>

        <div class="relative grid w-full max-w-4xl">
            <?php foreach ($slides as $i => $slide): ?>
                <figure class="col-start-1 row-start-1 transition duration-700"
                        :class="current === <?= $i ?> ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4 pointer-events-none'"
                        :aria-hidden="current !== <?= $i ?>" <?= $i ? 'x-cloak' : '' ?>>
                    <blockquote class="font-serif text-2xl italic leading-snug sm:text-4xl lg:text-5xl">
                        “<?= $this->e($slide['text'] ?? '') ?>”
                    </blockquote>
                    <figcaption class="mt-6 text-sm font-bold uppercase tracking-[0.25em] text-brand-300"><?= $this->e($slide['ref'] ?? '') ?></figcaption>
                </figure>
            <?php endforeach; ?>
        </div>

        <div class="mt-12 flex flex-col gap-4 sm:flex-row">
            <a href="#horarios" class="btn-primary px-8 py-4 text-base">Acompáñanos este domingo</a>
            <?php if ($liveUrl): ?>
                <a href="<?= $this->e($liveUrl) ?>" target="_blank" rel="noopener" class="btn-outline px-8 py-4 text-base">Ver en vivo</a>
            <?php elseif ($mapsUrl): ?>
                <a href="<?= $this->e($mapsUrl) ?>" target="_blank" rel="noopener" class="btn-outline px-8 py-4 text-base">Cómo llegar</a>
            <?php endif; ?>
        </div>

        <!-- Controles -->
        <div class="absolute inset-x-0 bottom-8 flex items-center justify-center gap-4">
            <button type="button" @click="go(current - 1)" class="grid h-9 w-9 place-items-center rounded-full border border-white/25 text-white/80 hover:bg-white/10" aria-label="Anterior">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="m15 19-7-7 7-7"/></svg>
            </button>
            <div class="flex gap-2">
                <?php foreach ($slides as $i => $slide): ?>
                    <button type="button" @click="go(<?= $i ?>)" class="h-1.5 rounded-full transition-all"
                            :class="current === <?= $i ?> ? 'w-8 bg-white' : 'w-3 bg-white/40 hover:bg-white/70'"
                            aria-label="Versículo <?= $i + 1 ?>"></button>
                <?php endforeach; ?>
            </div>
            <button type="button" @click="go(current + 1)" class="grid h-9 w-9 place-items-center rounded-full border border-white/25 text-white/80 hover:bg-white/10" aria-label="Siguiente">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="m9 5 7 7-7 7"/></svg>
            </button>
        </div>
    </div>
</section>
<?php else: ?>
<section class="relative isolate overflow-hidden bg-brand-900 text-white">
    <?php if ($heroImage): ?>
        <img src="<?= $this->e($heroImage) ?>" alt="" class="absolute inset-0 -z-20 h-full w-full object-cover animate-slow-zoom">
    <?php else: ?>
        <!-- Fondo decorativo cuando no hay imagen de portada configurada -->
        <div class="absolute inset-0 -z-20 bg-[radial-gradient(ellipse_at_top_right,_#1a939b_0%,_#15565a_45%,_#0a2729_100%)]"></div>
        <svg class="absolute inset-0 -z-10 h-full w-full opacity-[0.06]" aria-hidden="true">
            <defs><pattern id="hero-cross" width="56" height="56" patternUnits="userSpaceOnUse"><path d="M28 16v24M20 24h16" stroke="white" stroke-width="2" fill="none"/></pattern></defs>
            <rect width="100%" height="100%" fill="url(#hero-cross)"/>
        </svg>
    <?php endif; ?>
    <div class="absolute inset-0 -z-10 bg-gradient-to-b from-brand-950/70 via-brand-900/40 to-brand-950/90"></div>

    <div class="container-site flex min-h-[min(82vh,860px)] flex-col items-center justify-center py-28 text-center">
        <h1 class="animate-fade-up max-w-4xl text-4xl font-extrabold leading-tight tracking-tight sm:text-6xl lg:text-7xl">
            Bienvenido a <span class="font-serif italic font-medium text-brand-200"><?= $this->e($siteTitle ?? 'nuestra iglesia') ?></span>
        </h1>
        <p class="animate-fade-up [animation-delay:200ms] mt-6 max-w-2xl text-lg text-brand-50/90 sm:text-xl">
            <?= $this->e($tagline ?: 'Un lugar para conocer a Dios, crecer en comunidad y servir con propósito.') ?>
        </p>
        <div class="animate-fade-up [animation-delay:400ms] mt-10 flex flex-col gap-4 sm:flex-row">
            <a href="#horarios" class="btn-primary text-base px-8 py-4">Acompáñanos este domingo</a>
            <?php if ($liveUrl): ?>
                <a href="<?= $this->e($liveUrl) ?>" class="btn-outline text-base px-8 py-4">Ver en vivo</a>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>
