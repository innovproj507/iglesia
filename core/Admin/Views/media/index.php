<?php $this->layout('admin::layout', ['title' => $title, 'currentUser' => $currentUser, 'menuItems' => $menuItems, 'activeMenu' => $activeMenu, 'eyebrow' => 'Biblioteca']) ?>
<?php $icon = fn (string $name, string $class = 'h-5 w-5') => $this->fetch('admin::partials/icon', ['name' => $name, 'class' => $class]); ?>

<form method="post" action="/admin/media" enctype="multipart/form-data" class="card mb-6 grid gap-4 p-5 md:grid-cols-[1fr_1fr_auto] md:items-end">
    <?= csrf_field() ?>
    <label class="flex cursor-pointer items-center gap-4 rounded-2xl border-2 border-dashed border-gray-200 px-5 py-4 transition hover:border-brand-400 hover:bg-brand-50/40 dark:border-slate-700 dark:hover:bg-slate-800">
        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10"><?= $icon('upload') ?></span>
        <span class="min-w-0">
            <span class="block text-sm font-bold text-gray-900 dark:text-white">Elegir archivo</span>
            <span id="file-name" class="block truncate text-xs text-gray-400">Imágenes, audio, video o PDF · máx. 20 MB</span>
        </span>
        <input type="file" name="file" required class="sr-only" onchange="document.getElementById('file-name').textContent = this.files[0]?.name || ''">
    </label>
    <div>
        <label class="label">Texto alternativo</label>
        <input type="text" name="alt_text" placeholder="Describe la imagen" class="input">
    </div>
    <button type="submit" class="btn-primary"><?= $icon('upload', 'h-4 w-4') ?> Subir</button>
</form>

<div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-6">
    <?php foreach ($items as $item): ?>
        <div class="card group overflow-hidden">
            <div class="relative aspect-square bg-gray-100 dark:bg-slate-800">
                <?php if (str_starts_with($item['mime_type'], 'image/')): ?>
                    <img src="/<?= $this->e($item['file_path']) ?>" class="h-full w-full object-cover" alt="<?= $this->e($item['alt_text'] ?? '') ?>" loading="lazy">
                <?php else: ?>
                    <span class="grid h-full w-full place-items-center text-gray-400"><?= $icon('file', 'h-10 w-10') ?></span>
                <?php endif; ?>
                <div class="absolute inset-0 flex items-end justify-end gap-1 bg-gradient-to-t from-black/50 to-transparent p-2 opacity-0 transition group-hover:opacity-100">
                    <button type="button" onclick="navigator.clipboard.writeText('/<?= $this->e($item['file_path']) ?>'); this.textContent='¡Copiado!'" class="rounded-lg bg-white/90 px-2.5 py-1.5 text-[11px] font-bold text-gray-800">Copiar URL</button>
                    <form method="post" action="/admin/media/<?= (int) $item['id'] ?>/delete" onsubmit="return confirm('¿Eliminar este archivo?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="grid h-7 w-7 place-items-center rounded-lg bg-white/90 text-red-500" aria-label="Eliminar"><?= $icon('trash', 'h-4 w-4') ?></button>
                    </form>
                </div>
            </div>
            <div class="p-3">
                <p class="truncate text-xs font-semibold text-gray-800 dark:text-slate-200"><?= $this->e($item['file_name']) ?></p>
                <p class="text-[11px] text-gray-400"><?= number_format(($item['size'] ?? 0) / 1024, 0) ?> KB</p>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if (empty($items)): ?>
        <p class="col-span-full py-16 text-center text-gray-400">Sin archivos todavía.</p>
    <?php endif; ?>
</div>
