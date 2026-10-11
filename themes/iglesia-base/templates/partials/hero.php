<section class="relative bg-brand-800 text-white">
    <div class="absolute inset-0 bg-gradient-to-br from-brand-900/90 to-brand-700/80"></div>
    <div class="relative max-w-4xl mx-auto px-4 py-24 text-center">
        <h1 class="text-3xl sm:text-4xl font-bold mb-4"><?= $this->e($siteTitle ?? 'Bienvenido') ?></h1>
        <p class="text-lg text-brand-50 mb-8"><?= $this->e($tagline ?? 'Un lugar para crecer en comunidad y en fe') ?></p>
        <div class="flex items-center justify-center gap-4">
            <a href="/sermones" class="bg-white text-brand-800 font-medium px-6 py-3 rounded-md hover:bg-brand-50">
                Ver en Vivo
            </a>
            <a href="/conocenos" class="border border-white text-white font-medium px-6 py-3 rounded-md hover:bg-white/10">
                Conócenos
            </a>
        </div>
    </div>
</section>
