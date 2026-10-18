<?php
$meta = app(\Cms\Content\PostRepository::class)->allMeta((int) $post['id']);
?>
<div class="card space-y-5 p-6">
    <h3 class="card-title">Detalles del sermón</h3>
    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label class="label">Predicador</label>
            <input type="text" name="predicador" value="<?= $this->e($meta['predicador'] ?? '') ?>" class="input">
        </div>
        <div>
            <label class="label">Fecha de servicio</label>
            <input type="date" name="fecha_servicio" value="<?= $this->e($meta['fecha_servicio'] ?? '') ?>" class="input">
        </div>
    </div>
    <div>
        <label class="label">Serie</label>
        <input type="text" name="serie" placeholder="Ej. Fe en tiempos difíciles" class="input">
    </div>
    <div>
        <label class="label">Video (enlace de YouTube)</label>
        <input type="text" name="video_url" value="<?= $this->e($meta['video_url'] ?? '') ?>" placeholder="https://www.youtube.com/watch?v=…" class="input">
        <p class="hint">Pega el enlace normal del video; la miniatura y el reproductor se generan solos.</p>
    </div>
    <div>
        <label class="label">Audio (URL)</label>
        <input type="text" name="audio_url" value="<?= $this->e($meta['audio_url'] ?? '') ?>" class="input">
    </div>
</div>
