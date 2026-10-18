<?php $this->layout('admin::layout', ['title' => $title, 'currentUser' => $currentUser, 'menuItems' => $menuItems, 'activeMenu' => $activeMenu, 'eyebrow' => 'Sitio web', 'flash' => $flash]) ?>
<?php $icon = fn (string $name, string $class = 'h-5 w-5') => $this->fetch('admin::partials/icon', ['name' => $name, 'class' => $class]); ?>

<?php $this->start('actions') ?>
    <a href="/admin/mantenimiento/vista-previa" target="_blank" class="btn-secondary"><?= $icon('external', 'h-4 w-4') ?> Vista previa</a>
<?php $this->stop() ?>

<div class="grid gap-6 xl:grid-cols-3">
    <div class="space-y-6">
        <!-- Estado -->
        <section class="relative isolate overflow-hidden rounded-2xl p-6 text-white shadow-soft <?= $enabled ? 'bg-gradient-to-br from-amber-500 to-orange-600' : 'bg-gradient-to-br from-brand-600 to-brand-900' ?>">
            <svg class="absolute -right-6 -top-6 -z-10 h-40 w-40 text-white/10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path d="M21.75 6.75a4.5 4.5 0 0 1-4.88 4.49c-1.08-.09-2.27.07-2.96.91l-7.15 8.68a2.55 2.55 0 1 1-3.59-3.59l8.68-7.15c.84-.69 1-1.88.91-2.96a4.5 4.5 0 0 1 5.66-4.58L15 5.84l.4 2.76 2.76.4 3.29-3.29c.2.56.3 1.15.3 1.79Z"/></svg>
            <p class="text-xs font-bold uppercase tracking-widest text-white/70">Estado actual</p>
            <p class="mt-2 flex items-center gap-3 text-2xl font-extrabold">
                <span class="h-3 w-3 rounded-full <?= $enabled ? 'bg-white animate-pulse' : 'bg-emerald-300' ?>"></span>
                <?= $enabled ? 'En mantenimiento' : 'Sitio en línea' ?>
            </p>
            <p class="mt-2 text-sm text-white/80">
                <?= $enabled ? 'Los visitantes ven la página de mantenimiento. Tú y quienes tengan el enlace de acceso ven el sitio normal.' : 'Todos pueden ver el sitio. Activa el modo mantenimiento mientras haces cambios grandes.' ?>
            </p>
            <form method="post" action="/admin/mantenimiento/toggle" class="mt-6">
                <?= csrf_field() ?>
                <button type="submit" class="btn w-full bg-white py-3 <?= $enabled ? 'text-orange-700' : 'text-brand-800' ?> hover:bg-white/90"
                        <?= $enabled ? '' : 'onclick="return confirm(\'¿Activar el modo mantenimiento? Los visitantes no podrán ver el sitio.\')"' ?>>
                    <?= $enabled ? $icon('globe', 'h-4 w-4') . ' Volver a publicar el sitio' : $icon('wrench', 'h-4 w-4') . ' Activar mantenimiento' ?>
                </button>
            </form>
        </section>

        <!-- Enlace de acceso -->
        <section class="card p-6">
            <h2 class="card-title"><?= $icon('users', 'h-5 w-5 text-violet-500') ?> Enlace de acceso</h2>
            <p class="mt-2 text-sm text-gray-500 dark:text-slate-400">Compártelo con quien deba revisar el sitio durante el mantenimiento (por ejemplo, el pastor o un cliente). Funciona por 7 días en ese navegador, sin iniciar sesión.</p>
            <div class="mt-4 flex gap-2">
                <input type="text" readonly value="<?= $this->e($accessUrl) ?>" class="input font-mono text-xs" onclick="this.select()">
                <button type="button" class="btn-secondary shrink-0" onclick="navigator.clipboard.writeText('<?= $this->e($accessUrl) ?>'); this.textContent='¡Copiado!'">Copiar</button>
            </div>
            <form method="post" action="/admin/mantenimiento/token" class="mt-3" onsubmit="return confirm('El enlace anterior dejará de funcionar. ¿Continuar?')">
                <?= csrf_field() ?>
                <button type="submit" class="text-xs font-semibold text-gray-400 hover:text-red-500">Generar un enlace nuevo</button>
            </form>
        </section>
    </div>

    <!-- Contenido de la página -->
    <form method="post" action="/admin/mantenimiento" class="card space-y-5 p-6 xl:col-span-2">
        <?= csrf_field() ?>
        <h2 class="card-title"><?= $icon('pencil', 'h-5 w-5 text-brand-600') ?> Lo que verán los visitantes</h2>
        <div>
            <label class="label">Título</label>
            <input type="text" name="maint_title" value="<?= $this->e($maintTitle) ?>" placeholder="Estamos en mantenimiento" class="input">
        </div>
        <div>
            <label class="label">Mensaje</label>
            <textarea name="maint_message" rows="4" placeholder="Estamos mejorando nuestro sitio para servirte mejor. Volveremos muy pronto." class="input"><?= $this->e($maintMessage) ?></textarea>
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="label">Volvemos el… (opcional)</label>
                <input type="datetime-local" name="maint_until" value="<?= $this->e($maintUntil) ?>" class="input">
                <p class="hint">Muestra una cuenta regresiva. Al llegar la fecha el modo <strong>no</strong> se desactiva solo.</p>
            </div>
            <div class="flex items-start pt-8">
                <label class="flex cursor-pointer items-start gap-3">
                    <input type="checkbox" name="maint_show_contact" value="1" <?= $showContact ? 'checked' : '' ?> class="mt-0.5 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                    <span>
                        <span class="block text-sm font-semibold text-gray-800 dark:text-slate-200">Mostrar horarios y contacto</span>
                        <span class="block text-xs text-gray-400">Dirección, WhatsApp, teléfono y redes.</span>
                    </span>
                </label>
            </div>
        </div>
        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 pt-5 dark:border-slate-800">
            <p class="text-xs text-gray-400">El panel, las imágenes y la instalación siguen funcionando. Google recibe un aviso temporal (código 503) para no afectar el posicionamiento.</p>
            <button type="submit" class="btn-primary px-8"><?= $icon('check', 'h-4 w-4') ?> Guardar</button>
        </div>
    </form>
</div>
