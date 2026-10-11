<?php
$meta = app(\Cms\Content\PostRepository::class)->allMeta((int) $post['id']);
?>
<div class="bg-white rounded-lg shadow p-5 space-y-4">
    <h3 class="font-medium text-sm text-gray-700">Detalles del sermón</h3>
    <div>
        <label class="block text-sm font-medium mb-1">Predicador</label>
        <input type="text" name="predicador" value="<?= $this->e($meta['predicador'] ?? '') ?>"
               class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Serie</label>
        <input type="text" name="serie" placeholder="Ej. Fe en tiempos difíciles"
               class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Fecha de servicio</label>
        <input type="date" name="fecha_servicio" value="<?= $this->e($meta['fecha_servicio'] ?? '') ?>"
               class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">URL de audio</label>
        <input type="text" name="audio_url" value="<?= $this->e($meta['audio_url'] ?? '') ?>"
               class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">URL de video (YouTube/Vimeo embed)</label>
        <input type="text" name="video_url" value="<?= $this->e($meta['video_url'] ?? '') ?>"
               class="w-full border rounded px-3 py-2 text-sm">
    </div>
</div>
