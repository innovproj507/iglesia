<section class="relative isolate overflow-hidden bg-gradient-to-br from-brand-700 via-brand-800 to-brand-950 text-white">
    <svg class="absolute inset-0 -z-10 h-full w-full opacity-[0.06]" aria-hidden="true">
        <defs><pattern id="ph-cross" width="56" height="56" patternUnits="userSpaceOnUse"><path d="M28 16v24M20 24h16" stroke="white" stroke-width="2" fill="none"/></pattern></defs>
        <rect width="100%" height="100%" fill="url(#ph-cross)"/>
    </svg>
    <div class="container-site py-16 sm:py-20 <?= ($center ?? true) ? 'text-center' : '' ?>">
        <?php if (!empty($eyebrow)): ?>
            <span class="eyebrow text-brand-300"><?= $this->e($eyebrow) ?></span>
        <?php endif; ?>
        <h1 class="mt-2 mx-auto max-w-4xl text-3xl font-extrabold tracking-tight sm:text-5xl"><?= $this->e($heading ?? '') ?></h1>
        <?php if (!empty($subheading)): ?>
            <p class="mx-auto mt-4 max-w-2xl text-lg text-brand-100/90"><?= $this->e($subheading) ?></p>
        <?php endif; ?>
    </div>
</section>
