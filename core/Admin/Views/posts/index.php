<?php $this->layout('admin::layout', ['title' => $title, 'currentUser' => $currentUser, 'menuItems' => $menuItems, 'activeMenu' => $activeMenu, 'eyebrow' => 'Contenido']) ?>
<?php
$icon = fn (string $name, string $class = 'h-5 w-5') => $this->fetch('admin::partials/icon', ['name' => $name, 'class' => $class]);
$isAll = $postType === 'all';
$statusLabels = ['published' => ['Publicado', 'badge-green'], 'draft' => ['Borrador', 'badge-amber'], 'scheduled' => ['Programado', 'badge-brand'], 'trashed' => ['Papelera', 'badge-gray']];
$typeLabel = fn (string $slug) => $postTypes->get($slug)?->labelSingular ?? ucfirst($slug);
$filterUrl = fn (?string $s) => '/admin/posts?' . http_build_query(array_filter(['type' => $postType, 'q' => $search, 'status' => $s]));
?>

<?php $this->start('actions') ?>
    <?php if (!$isAll): ?>
        <a href="/admin/posts/new?type=<?= $this->e($postType) ?>" class="btn-primary"><?= $icon('plus', 'h-4 w-4') ?> Crear nuevo</a>
    <?php endif; ?>
<?php $this->stop() ?>

<div class="card overflow-hidden">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 px-5 py-4 dark:border-slate-800">
        <div class="flex flex-wrap gap-1 rounded-xl bg-gray-100 p-1 dark:bg-slate-800">
            <?php foreach ([null => 'Todos', 'published' => 'Publicados', 'draft' => 'Borradores'] as $value => $label): ?>
                <?php $on = ($status ?? '') === (string) $value; ?>
                <a href="<?= $this->e($filterUrl($value ?: null)) ?>" class="rounded-lg px-3 py-1.5 text-xs font-bold transition <?= $on ? 'bg-white text-gray-900 shadow-sm dark:bg-slate-900 dark:text-white' : 'text-gray-500 hover:text-gray-900 dark:text-slate-400' ?>"><?= $label ?></a>
            <?php endforeach; ?>
        </div>
        <form method="get" action="/admin/posts" class="relative w-full sm:w-72">
            <input type="hidden" name="type" value="<?= $this->e($postType) ?>">
            <?php if ($status): ?><input type="hidden" name="status" value="<?= $this->e($status) ?>"><?php endif; ?>
            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><?= $icon('search', 'h-4 w-4') ?></span>
            <input type="search" name="q" value="<?= $this->e($search) ?>" placeholder="Buscar por título…" class="input pl-9">
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="table">
            <thead>
                <tr>
                    <th>Título</th>
                    <?php if ($isAll): ?><th>Tipo</th><?php endif; ?>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th class="text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                    <?php [$sLabel, $sClass] = $statusLabels[$post['status']] ?? [$post['status'], 'badge-gray']; ?>
                    <tr>
                        <td>
                            <a href="/admin/posts/<?= (int) $post['id'] ?>/edit" class="font-bold text-gray-900 hover:text-brand-600 dark:text-white"><?= $this->e($post['title']) ?></a>
                            <span class="block text-xs text-gray-400">/<?= $this->e($post['slug']) ?></span>
                        </td>
                        <?php if ($isAll): ?><td><span class="badge-gray"><?= $this->e($typeLabel($post['post_type'])) ?></span></td><?php endif; ?>
                        <td><span class="<?= $sClass ?>"><?= $this->e($sLabel) ?></span></td>
                        <td class="whitespace-nowrap text-gray-500"><?= $this->e(format_date($post['published_at'] ?? $post['created_at'])) ?></td>
                        <td>
                            <div class="flex items-center justify-end gap-1">
                                <a href="/admin/posts/<?= (int) $post['id'] ?>/edit" class="icon-btn h-9 w-9" title="Editar" aria-label="Editar"><?= $icon('pencil', 'h-4 w-4') ?></a>
                                <form method="post" action="/admin/posts/<?= (int) $post['id'] ?>/delete" onsubmit="return confirm('¿Eliminar este contenido?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="icon-btn h-9 w-9 hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-500/10" title="Eliminar" aria-label="Eliminar"><?= $icon('trash', 'h-4 w-4') ?></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($posts)): ?>
                    <tr>
                        <td colspan="5" class="py-16 text-center">
                            <span class="mx-auto mb-3 grid h-14 w-14 place-items-center rounded-2xl bg-gray-100 text-gray-400 dark:bg-slate-800"><?= $icon('folder', 'h-7 w-7') ?></span>
                            <p class="font-semibold text-gray-700 dark:text-slate-300"><?= $search !== '' ? 'No encontramos resultados' : 'Aún no hay contenido aquí' ?></p>
                            <?php if (!$isAll): ?>
                                <a href="/admin/posts/new?type=<?= $this->e($postType) ?>" class="link-primary mt-2 inline-block">Crear el primero →</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if (!empty($posts)): ?>
        <p class="border-t border-gray-100 px-5 py-3 text-xs text-gray-400 dark:border-slate-800"><?= (int) $total ?> en total</p>
    <?php endif; ?>
</div>
