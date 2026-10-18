<div class="mb-6 flex flex-wrap items-center gap-4 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-900 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-200">
    <span class="relative flex h-3 w-3 shrink-0">
        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-amber-400 opacity-75"></span>
        <span class="relative inline-flex h-3 w-3 rounded-full bg-amber-500"></span>
    </span>
    <p class="flex-1"><strong>El sitio está en modo mantenimiento.</strong> Los visitantes ven la página de “Estamos en mantenimiento”; tú sigues viendo el sitio normal.</p>
    <form method="post" action="/admin/mantenimiento/toggle">
        <?= csrf_field() ?>
        <button type="submit" class="rounded-xl bg-amber-500 px-4 py-2 text-xs font-bold text-white hover:bg-amber-600">Desactivar ahora</button>
    </form>
</div>
