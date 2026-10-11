<?php $this->layout('admin::layout', ['title' => $title, 'currentUser' => $currentUser, 'menuItems' => $menuItems, 'activeMenu' => $activeMenu]) ?>

<form method="post" action="<?= $post ? '/admin/posts/' . (int) $post['id'] : '/admin/posts' ?>" class="space-y-6 max-w-3xl">
    <?= csrf_field() ?>
    <input type="hidden" name="post_type" value="<?= $this->e($postType) ?>">

    <div class="bg-white rounded-lg shadow p-5 space-y-4">
        <div>
            <label class="block text-sm font-medium mb-1">Título</label>
            <input type="text" name="title" value="<?= $this->e($post['title'] ?? '') ?>" required
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Slug</label>
            <input type="text" name="slug" value="<?= $this->e($post['slug'] ?? '') ?>"
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Contenido</label>
            <textarea name="content" rows="12" class="w-full border rounded px-3 py-2 text-sm font-mono"><?= $this->e($post['content'] ?? '') ?></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Extracto</label>
            <textarea name="excerpt" rows="3" class="w-full border rounded px-3 py-2 text-sm"><?= $this->e($post['excerpt'] ?? '') ?></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Estado</label>
            <select name="status" class="border rounded px-3 py-2 text-sm">
                <?php foreach (['draft' => 'Borrador', 'published' => 'Publicado'] as $value => $label): ?>
                    <option value="<?= $value ?>" <?= ($post['status'] ?? 'draft') === $value ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <?php if ($post): ?>
        <?php do_action('admin.post.edit.meta_boxes', $post); ?>
    <?php endif; ?>

    <button type="submit" class="bg-brand-700 text-white px-5 py-2 rounded text-sm hover:bg-brand-800">
        Guardar
    </button>
</form>
