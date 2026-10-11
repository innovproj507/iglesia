<?php $this->layout('admin::layout', ['title' => $title, 'currentUser' => $currentUser, 'menuItems' => $menuItems, 'activeMenu' => $activeMenu]) ?>

<div class="flex justify-between items-center mb-4">
    <h2 class="text-lg font-medium"><?= $this->e($postTypeLabel) ?></h2>
    <a href="/admin/posts/new?type=<?= $this->e($postType) ?>" class="bg-brand-700 text-white px-4 py-2 rounded text-sm hover:bg-brand-800">
        Nueva
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm text-left">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="px-4 py-2">Título</th>
                <th class="px-4 py-2">Estado</th>
                <th class="px-4 py-2">Fecha</th>
                <th class="px-4 py-2"></th>
            </tr>
        </thead>
        <tbody class="divide-y">
            <?php foreach ($posts as $post): ?>
                <tr>
                    <td class="px-4 py-2"><?= $this->e($post['title']) ?></td>
                    <td class="px-4 py-2"><?= $this->e($post['status']) ?></td>
                    <td class="px-4 py-2"><?= $this->e($post['published_at'] ?? $post['created_at']) ?></td>
                    <td class="px-4 py-2 text-right space-x-2">
                        <a href="/admin/posts/<?= (int) $post['id'] ?>/edit" class="text-brand-700 hover:underline">Editar</a>
                        <form method="post" action="/admin/posts/<?= (int) $post['id'] ?>/delete" class="inline" onsubmit="return confirm('¿Eliminar este contenido?')">
                            <?= csrf_field() ?>
                            <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($posts)): ?>
                <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">Sin contenido todavía.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
