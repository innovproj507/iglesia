<?php $this->layout('admin::layout', ['title' => $title, 'currentUser' => $currentUser, 'menuItems' => $menuItems, 'activeMenu' => $activeMenu, 'eyebrow' => 'Configuración']) ?>
<?php $icon = fn (string $name, string $class = 'h-5 w-5') => $this->fetch('admin::partials/icon', ['name' => $name, 'class' => $class]); ?>

<form method="post" action="/admin/settings" class="grid gap-6 xl:grid-cols-3">
    <?= csrf_field() ?>

    <div class="space-y-6 xl:col-span-2">
        <section class="card p-6">
            <h2 class="card-title mb-5"><span class="grid h-8 w-8 place-items-center rounded-lg bg-brand-50 text-brand-600 dark:bg-brand-500/10"><?= $icon('globe', 'h-4 w-4') ?></span> Identidad del sitio</h2>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="label">Nombre del sitio</label>
                    <input type="text" name="site_title" value="<?= $this->e($siteTitle) ?>" class="input">
                </div>
                <div>
                    <label class="label">Lema</label>
                    <input type="text" name="tagline" value="<?= $this->e($tagline) ?>" class="input">
                </div>
                <div class="sm:col-span-2">
                    <label class="label">Logo</label>
                    <div class="flex items-center gap-4">
                        <?php if ($logo): ?>
                            <span class="grid h-12 w-28 shrink-0 place-items-center rounded-xl bg-gray-50 p-2 dark:bg-white"><img src="<?= $this->e($logo) ?>" alt="" class="max-h-full"></span>
                        <?php endif; ?>
                        <input type="text" name="logo" value="<?= $this->e($logo) ?>" placeholder="/assets/brand/logo.png" class="input">
                    </div>
                </div>
            </div>
        </section>

        <section class="card p-6">
            <h2 class="card-title mb-5"><span class="grid h-8 w-8 place-items-center rounded-lg bg-violet-50 text-violet-600 dark:bg-violet-500/10"><?= $icon('home', 'h-4 w-4') ?></span> Página de inicio</h2>
            <div class="space-y-5">
                <div>
                    <label class="label">Texto de bienvenida</label>
                    <textarea name="welcome_text" rows="5" class="input"><?= $this->e($welcomeText) ?></textarea>
                    <p class="hint">Deja una línea en blanco para separar párrafos.</p>
                </div>
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="label">Imagen de portada</label>
                        <input type="text" name="hero_image" value="<?= $this->e($heroImage) ?>" placeholder="/uploads/…/foto.jpg" class="input">
                        <p class="hint">Copia la URL desde Media.</p>
                    </div>
                    <div>
                        <label class="label">Transmisión en vivo</label>
                        <input type="text" name="live_url" value="<?= $this->e($liveUrl) ?>" placeholder="https://facebook.com/…/live" class="input">
                        <p class="hint">Si se llena, aparece el botón rojo “En vivo”.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="card p-6">
            <h2 class="card-title mb-5"><span class="grid h-8 w-8 place-items-center rounded-lg bg-earth-50 text-earth-600 dark:bg-earth-500/10"><?= $icon('clock', 'h-4 w-4') ?></span> Horarios</h2>
            <label class="label">Reuniones (una por línea)</label>
            <textarea name="service_times" rows="4" class="input font-mono text-[13px]"><?= $this->e(implode("\n", $serviceTimes)) ?></textarea>
            <p class="hint">Formato: <code class="rounded bg-gray-100 px-1 dark:bg-slate-800">Título | Horario | Día</code> — ej. <code class="rounded bg-gray-100 px-1 dark:bg-slate-800">Culto principal | 11:00 AM | Todos los domingos</code>. El día alimenta el calendario del panel.</p>
        </section>
    </div>

    <div class="space-y-6">
        <section class="card p-6">
            <h2 class="card-title mb-5"><span class="grid h-8 w-8 place-items-center rounded-lg bg-sky-50 text-sky-600 dark:bg-sky-500/10"><?= $icon('users', 'h-4 w-4') ?></span> Contacto</h2>
            <div class="space-y-4">
                <div><label class="label">Dirección</label><input type="text" name="address" value="<?= $this->e($address) ?>" class="input"></div>
                <div><label class="label">Teléfono</label><input type="text" name="phone" value="<?= $this->e($phone) ?>" class="input"></div>
                <div><label class="label">Correo</label><input type="email" name="email" value="<?= $this->e($email) ?>" class="input"></div>
                <div><label class="label">WhatsApp</label><input type="text" name="whatsapp" value="<?= $this->e($whatsapp) ?>" placeholder="+507 …" class="input"></div>
            </div>
        </section>

        <section class="card p-6">
            <h2 class="card-title mb-5"><span class="grid h-8 w-8 place-items-center rounded-lg bg-rose-50 text-rose-600 dark:bg-rose-500/10"><?= $icon('heart', 'h-4 w-4') ?></span> Redes sociales</h2>
            <div class="space-y-4">
                <?php foreach (['facebook' => 'Facebook', 'instagram' => 'Instagram', 'youtube' => 'YouTube', 'x' => 'X / Twitter'] as $key => $label): ?>
                    <div><label class="label"><?= $label ?></label><input type="url" name="<?= $key ?>" value="<?= $this->e($socialLinks[$key] ?? '') ?>" placeholder="https://" class="input"></div>
                <?php endforeach; ?>
            </div>
        </section>

        <div class="xl:sticky xl:top-28">
            <button type="submit" class="btn-primary w-full py-3"><?= $icon('check', 'h-4 w-4') ?> Guardar cambios</button>
        </div>
    </div>
</form>
