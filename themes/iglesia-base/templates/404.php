<?php $this->insert('partials/header', array_merge(get_defined_vars(), ['pageTitle' => 'Página no encontrada'])) ?>

<section class="container-site grid min-h-[60vh] place-items-center py-24 text-center">
    <div>
        <p class="font-serif text-8xl italic text-brand-200">404</p>
        <h1 class="mt-4 text-3xl font-bold text-gray-900">No encontramos esta página</h1>
        <p class="mx-auto mt-3 max-w-md text-gray-500">Puede que se haya movido o ya no exista. Pero hay mucho más por descubrir.</p>
        <div class="mt-8 flex flex-wrap justify-center gap-4">
            <a href="/" class="btn-primary">Volver al inicio</a>
            <a href="/sermones" class="btn-ghost">Ver sermones</a>
        </div>
    </div>
</section>

<?php $this->insert('partials/footer', get_defined_vars()) ?>
