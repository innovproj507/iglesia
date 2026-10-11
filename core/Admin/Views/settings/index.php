<?php $this->layout('admin::layout', ['title' => $title, 'currentUser' => $currentUser, 'menuItems' => $menuItems, 'activeMenu' => $activeMenu]) ?>

<form method="post" action="/admin/settings" class="bg-white rounded-lg shadow p-5 space-y-4 max-w-xl">
    <?= csrf_field() ?>
    <div>
        <label class="block text-sm font-medium mb-1">Nombre del sitio</label>
        <input type="text" name="site_title" value="<?= $this->e($siteTitle) ?>" class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Lema / tagline</label>
        <input type="text" name="tagline" value="<?= $this->e($tagline) ?>" class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Horarios de servicio (uno por línea)</label>
        <textarea name="service_times" rows="4" class="w-full border rounded px-3 py-2 text-sm"><?= $this->e(implode("\n", $serviceTimes)) ?></textarea>
    </div>
    <div class="grid grid-cols-3 gap-3">
        <div>
            <label class="block text-sm font-medium mb-1">Facebook</label>
            <input type="text" name="facebook" value="<?= $this->e($socialLinks['facebook'] ?? '') ?>" class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Instagram</label>
            <input type="text" name="instagram" value="<?= $this->e($socialLinks['instagram'] ?? '') ?>" class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">YouTube</label>
            <input type="text" name="youtube" value="<?= $this->e($socialLinks['youtube'] ?? '') ?>" class="w-full border rounded px-3 py-2 text-sm">
        </div>
    </div>
    <button type="submit" class="bg-brand-700 text-white px-5 py-2 rounded text-sm hover:bg-brand-800">Guardar</button>
</form>
