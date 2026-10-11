<?php $this->layout('admin::layout', ['title' => $title, 'currentUser' => $currentUser, 'menuItems' => $menuItems, 'activeMenu' => $activeMenu]) ?>

<form method="post" action="/admin/media" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-5 mb-6 flex items-end gap-3">
    <?= csrf_field() ?>
    <div class="flex-1">
        <label class="block text-sm font-medium mb-1">Archivo</label>
        <input type="file" name="file" required class="w-full text-sm">
    </div>
    <div class="flex-1">
        <label class="block text-sm font-medium mb-1">Texto alternativo</label>
        <input type="text" name="alt_text" class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <button type="submit" class="bg-brand-700 text-white px-4 py-2 rounded text-sm hover:bg-brand-800">Subir</button>
</form>

<div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-4">
    <?php foreach ($items as $item): ?>
        <div class="bg-white rounded-lg shadow p-2 text-center">
            <?php if (str_starts_with($item['mime_type'], 'image/')): ?>
                <img src="/<?= $this->e($item['file_path']) ?>" class="w-full h-24 object-cover rounded mb-2" alt="<?= $this->e($item['alt_text'] ?? '') ?>">
            <?php else: ?>
                <div class="w-full h-24 bg-gray-100 rounded mb-2 flex items-center justify-center text-xs text-gray-500">
                    <?= $this->e($item['mime_type']) ?>
                </div>
            <?php endif; ?>
            <div class="text-xs truncate mb-2"><?= $this->e($item['file_name']) ?></div>
            <form method="post" action="/admin/media/<?= (int) $item['id'] ?>/delete" onsubmit="return confirm('¿Eliminar?')">
                <?= csrf_field() ?>
                <button type="submit" class="text-red-600 text-xs hover:underline">Eliminar</button>
            </form>
        </div>
    <?php endforeach; ?>
    <?php if (empty($items)): ?>
        <p class="text-gray-400 col-span-full text-center py-6">Sin archivos todavía.</p>
    <?php endif; ?>
</div>
