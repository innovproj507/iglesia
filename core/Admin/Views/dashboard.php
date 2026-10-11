<?php $this->layout('admin::layout', ['title' => $title, 'currentUser' => $currentUser, 'menuItems' => $menuItems, 'activeMenu' => $activeMenu]) ?>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white rounded-lg shadow p-5">
        <div class="text-sm text-gray-500">Entradas</div>
        <div class="text-2xl font-semibold"><?= (int) $postCount ?></div>
    </div>
    <div class="bg-white rounded-lg shadow p-5">
        <div class="text-sm text-gray-500">Páginas</div>
        <div class="text-2xl font-semibold"><?= (int) $pageCount ?></div>
    </div>
    <div class="bg-white rounded-lg shadow p-5">
        <div class="text-sm text-gray-500">Archivos multimedia</div>
        <div class="text-2xl font-semibold"><?= (int) $mediaCount ?></div>
    </div>
    <div class="bg-white rounded-lg shadow p-5">
        <div class="text-sm text-gray-500">Usuarios</div>
        <div class="text-2xl font-semibold"><?= (int) $userCount ?></div>
    </div>
</div>
