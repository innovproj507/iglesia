<?php $this->layout('admin::layout', ['title' => $title, 'currentUser' => $currentUser, 'menuItems' => $menuItems, 'activeMenu' => $activeMenu, 'eyebrow' => 'Posicionamiento', 'flash' => $flash]) ?>
<?php
$icon = fn (string $name, string $class = 'h-5 w-5') => $this->fetch('admin::partials/icon', ['name' => $name, 'class' => $class]);
$circ = 2 * M_PI * 42;
$scoreColor = $score >= 80 ? '#10b981' : ($score >= 50 ? '#f59e0b' : '#ef4444');
$host = parse_url($baseUrl, PHP_URL_HOST);
?>

<?php $this->start('actions') ?>
    <a href="/sitemap.xml" target="_blank" class="btn-secondary"><?= $icon('external', 'h-4 w-4') ?> sitemap.xml</a>
    <a href="/robots.txt" target="_blank" class="btn-secondary"><?= $icon('external', 'h-4 w-4') ?> robots.txt</a>
<?php $this->stop() ?>

<div class="grid gap-6 xl:grid-cols-3">
    <!-- Salud SEO -->
    <section class="card p-6 xl:col-span-1">
        <h2 class="card-title"><?= $icon('chart', 'h-5 w-5 text-brand-600') ?> Salud SEO</h2>
        <div class="mt-5 flex items-center gap-5">
            <div class="relative h-28 w-28 shrink-0">
                <svg viewBox="0 0 100 100" class="h-full w-full -rotate-90">
                    <circle cx="50" cy="50" r="42" fill="none" stroke-width="10" class="stroke-gray-100 dark:stroke-slate-800"/>
                    <circle cx="50" cy="50" r="42" fill="none" stroke="<?= $scoreColor ?>" stroke-width="10" stroke-linecap="round" stroke-dasharray="<?= $score / 100 * $circ ?> <?= $circ ?>"/>
                </svg>
                <span class="absolute inset-0 grid place-items-center text-2xl font-extrabold text-gray-900 dark:text-white"><?= $score ?></span>
            </div>
            <p class="text-sm text-gray-500 dark:text-slate-400">
                <?= $score >= 80 ? '¡Muy bien! El sitio está listo para Google.' : ($score >= 50 ? 'Vas bien. Completa los puntos pendientes para mejorar.' : 'Hay varias cosas por configurar.') ?>
            </p>
        </div>
        <ul class="mt-6 space-y-3">
            <?php foreach ($checks as [$label, $ok, $hint]): ?>
                <li class="flex gap-3">
                    <span class="mt-0.5 grid h-5 w-5 shrink-0 place-items-center rounded-full <?= $ok ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15' : 'bg-amber-100 text-amber-600 dark:bg-amber-500/15' ?>">
                        <?= $ok ? $icon('check', 'h-3 w-3') : '<span class="text-[11px] font-bold">!</span>' ?>
                    </span>
                    <span>
                        <span class="block text-sm font-semibold text-gray-800 dark:text-slate-200"><?= $this->e($label) ?></span>
                        <span class="block text-xs text-gray-400"><?= $this->e($hint) ?></span>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php if ($missing): ?>
            <div class="mt-6 border-t border-gray-100 pt-5 dark:border-slate-800">
                <p class="mb-3 text-xs font-bold uppercase tracking-wider text-gray-400">Falta descripción</p>
                <ul class="space-y-1">
                    <?php foreach ($missing as $item): ?>
                        <li><a href="/admin/posts/<?= (int) $item['id'] ?>/edit" class="flex items-center justify-between rounded-lg px-2 py-1.5 text-sm hover:bg-gray-50 dark:hover:bg-slate-800">
                            <span class="truncate font-medium text-gray-700 dark:text-slate-300"><?= $this->e($item['title']) ?></span>
                            <span class="text-brand-600"><?= $icon('chevron-right', 'h-4 w-4') ?></span>
                        </a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </section>

    <form method="post" action="/admin/seo" class="space-y-6 xl:col-span-2">
        <?= csrf_field() ?>

        <!-- Vista previa -->
        <section class="card p-6">
            <h2 class="card-title mb-4"><?= $icon('search', 'h-5 w-5 text-brand-600') ?> Así te ve Google</h2>
            <div class="rounded-2xl border border-gray-100 bg-white p-5 dark:border-slate-800 dark:bg-slate-950">
                <div class="flex items-center gap-3">
                    <span class="grid h-7 w-7 place-items-center rounded-full bg-gray-100 text-[10px] font-bold text-gray-500"><?= $this->e(mb_strtoupper(mb_substr(option('site_title', 'I'), 0, 1))) ?></span>
                    <span class="leading-tight">
                        <span class="block text-sm text-gray-800 dark:text-slate-200"><?= $this->e(option('site_title', '')) ?></span>
                        <span class="block text-xs text-gray-500"><?= $this->e($baseUrl) ?></span>
                    </span>
                </div>
                <p class="mt-2 text-xl text-[#1a0dab] dark:text-[#8ab4f8]"><?= $this->e($home->title) ?></p>
                <p id="preview-desc" class="mt-1 text-sm text-gray-600 dark:text-slate-400"><?= $this->e($home->description ?: 'Escribe una descripción del sitio…') ?></p>
            </div>
        </section>

        <section class="card space-y-5 p-6">
            <h2 class="card-title"><?= $icon('globe', 'h-5 w-5 text-violet-500') ?> General</h2>
            <div>
                <label class="label">Descripción del sitio</label>
                <textarea name="seo_default_description" rows="3" maxlength="300" class="input" oninput="document.getElementById('preview-desc').textContent = this.value; document.getElementById('desc-count').textContent = this.value.length"><?= $this->e($values['seo_default_description']) ?></textarea>
                <p class="hint"><span id="desc-count"><?= mb_strlen($values['seo_default_description']) ?></span>/160 caracteres recomendados. Se usa en el inicio y en páginas sin descripción propia.</p>
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="label">Formato del título</label>
                    <input type="text" name="seo_title_format" value="<?= $this->e($values['seo_title_format']) ?>" class="input" placeholder="{title} | {site}">
                    <p class="hint"><code>{title}</code> = título de la página, <code>{site}</code> = nombre del sitio.</p>
                </div>
                <div>
                    <label class="label">Imagen para compartir</label>
                    <input type="text" name="seo_default_image" value="<?= $this->e($values['seo_default_image']) ?>" class="input" placeholder="/uploads/…/foto.jpg">
                    <p class="hint">Ideal 1200×630. Si está vacío se usa la imagen de portada.</p>
                </div>
            </div>
            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-red-100 bg-red-50/50 p-4 dark:border-red-500/20 dark:bg-red-500/5">
                <input type="checkbox" name="seo_noindex" value="1" <?= $values['seo_noindex'] ? 'checked' : '' ?> class="mt-0.5 rounded border-gray-300 text-red-500 focus:ring-red-500">
                <span>
                    <span class="block text-sm font-semibold text-gray-800 dark:text-slate-200">Ocultar el sitio de los buscadores</span>
                    <span class="block text-xs text-gray-500">Úsalo solo mientras el sitio está en construcción.</span>
                </span>
            </label>
        </section>

        <section class="card space-y-5 p-6">
            <h2 class="card-title"><?= $icon('chart', 'h-5 w-5 text-earth-500') ?> Google y analítica</h2>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="label">Google Analytics (ID de medición)</label>
                    <input type="text" name="seo_ga_id" value="<?= $this->e($values['seo_ga_id']) ?>" class="input" placeholder="G-XXXXXXXXXX">
                    <p class="hint">Las visitas de quienes inician sesión en el panel no se cuentan.</p>
                </div>
                <div>
                    <label class="label">Verificación de Google Search Console</label>
                    <input type="text" name="seo_google_verification" value="<?= $this->e($values['seo_google_verification']) ?>" class="input" placeholder="Código de la etiqueta meta">
                    <p class="hint">Solo el valor de <code>content="…"</code>.</p>
                </div>
                <div>
                    <label class="label">Verificación de Bing</label>
                    <input type="text" name="seo_bing_verification" value="<?= $this->e($values['seo_bing_verification']) ?>" class="input">
                </div>
                <div>
                    <label class="label">Reglas extra para robots.txt</label>
                    <textarea name="seo_robots_extra" rows="2" class="input font-mono text-xs" placeholder="Disallow: /privado"><?= $this->e($values['seo_robots_extra']) ?></textarea>
                </div>
            </div>
            <div class="rounded-xl bg-brand-50 p-4 text-sm text-brand-800 dark:bg-brand-500/10 dark:text-brand-200">
                <strong>Siguiente paso:</strong> en Google Search Console agrega tu dominio y envía el mapa del sitio:
                <code class="ml-1 rounded bg-white px-1.5 py-0.5 text-xs dark:bg-slate-900"><?= $this->e($baseUrl) ?>/sitemap.xml</code>
            </div>
        </section>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary px-8"><?= $icon('check', 'h-4 w-4') ?> Guardar</button>
        </div>
    </form>
</div>
