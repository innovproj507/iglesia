<?php $this->layout('admin::layout', ['title' => $title, 'currentUser' => $currentUser, 'menuItems' => $menuItems, 'activeMenu' => $activeMenu, 'eyebrow' => 'Contenido']) ?>
<?php
$icon = fn (string $name, string $class = 'h-5 w-5') => $this->fetch('admin::partials/icon', ['name' => $name, 'class' => $class]);
$publicUrl = null;
if ($post && $post['status'] === 'published') {
    $publicUrl = match ($post['post_type']) {
        'sermon' => '/sermones/' . $post['slug'],
        'page', 'post' => '/' . $post['slug'],
        default => null,
    };
}
?>

<?php $this->start('actions') ?>
    <a href="/admin/posts?type=<?= $this->e($postType) ?>" class="btn-secondary">← Volver</a>
    <?php if ($publicUrl): ?>
        <a href="<?= $this->e($publicUrl) ?>" target="_blank" class="btn-secondary"><?= $icon('external', 'h-4 w-4') ?> Ver en el sitio</a>
    <?php endif; ?>
<?php $this->stop() ?>

<form method="post" action="<?= $post ? '/admin/posts/' . (int) $post['id'] : '/admin/posts' ?>" class="grid gap-6 lg:grid-cols-3">
    <?= csrf_field() ?>
    <input type="hidden" name="post_type" value="<?= $this->e($postType) ?>">

    <div class="space-y-6 lg:col-span-2">
        <div class="card space-y-5 p-6">
            <div>
                <label class="label" for="title">Título</label>
                <input id="title" type="text" name="title" value="<?= $this->e($post['title'] ?? '') ?>" required class="input text-base font-semibold">
            </div>
            <div>
                <label class="label" for="content">Contenido</label>
                <textarea id="content" name="content" rows="16" class="input font-mono text-[13px] leading-relaxed"><?= $this->e($post['content'] ?? '') ?></textarea>
                <p class="hint">Admite HTML: &lt;h2&gt;, &lt;p&gt;, &lt;strong&gt;, &lt;blockquote&gt;, &lt;a href=""&gt;…</p>
            </div>
            <div>
                <label class="label" for="excerpt">Extracto</label>
                <textarea id="excerpt" name="excerpt" rows="3" class="input"><?= $this->e($post['excerpt'] ?? '') ?></textarea>
                <p class="hint">Resumen corto que aparece en tarjetas y encabezados.</p>
            </div>
        </div>

        <?php if ($post): ?>
            <?php do_action('admin.post.edit.meta_boxes', $post); ?>
        <?php endif; ?>
    </div>

    <aside class="space-y-6">
        <div class="card space-y-5 p-6 lg:sticky lg:top-28">
            <h2 class="card-title">Publicación</h2>
            <div>
                <label class="label" for="status">Estado</label>
                <select id="status" name="status" class="input">
                    <?php foreach (['draft' => 'Borrador', 'published' => 'Publicado'] as $value => $label): ?>
                        <option value="<?= $value ?>" <?= ($post['status'] ?? 'draft') === $value ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="label" for="slug">Slug (URL)</label>
                <input id="slug" type="text" name="slug" value="<?= $this->e($post['slug'] ?? '') ?>" placeholder="se-genera-del-titulo" class="input">
            </div>
            <?php if ($post): ?>
                <dl class="space-y-2 rounded-xl bg-gray-50 p-4 text-xs dark:bg-slate-800/60">
                    <div class="flex justify-between"><dt class="text-gray-400">Creado</dt><dd class="font-semibold"><?= $this->e(format_date($post['created_at'])) ?></dd></div>
                    <div class="flex justify-between"><dt class="text-gray-400">Actualizado</dt><dd class="font-semibold"><?= $this->e(time_ago($post['updated_at'])) ?></dd></div>
                </dl>
            <?php else: ?>
                <p class="hint">Los campos extra (predicador, video…) aparecen después de guardar por primera vez.</p>
            <?php endif; ?>
            <button type="submit" class="btn-primary w-full"><?= $icon('check', 'h-4 w-4') ?> Guardar</button>
        </div>
    </aside>
</form>
