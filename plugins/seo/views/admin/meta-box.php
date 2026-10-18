<?php
$seoTitle = $meta['seo_title'] ?? '';
$seoDesc = $meta['seo_description'] ?? '';
$path = match ($post['post_type']) {
    'sermon' => '/sermones/' . $post['slug'],
    default => '/' . $post['slug'],
};
$fallbackDesc = mb_strimwidth(trim(strip_tags($post['excerpt'] ?: ($post['content'] ?? ''))), 0, 160, '…');
?>
<div class="card space-y-5 p-6" id="seo-box">
    <div class="flex items-center justify-between">
        <h3 class="card-title">SEO · Google y redes sociales</h3>
        <span class="badge-brand">Opcional</span>
    </div>

    <div class="rounded-2xl border border-gray-100 bg-white p-4 dark:border-slate-800 dark:bg-slate-950">
        <p class="truncate text-xs text-gray-500"><?= $this->e(($_SERVER['HTTP_HOST'] ?? '') . $path) ?></p>
        <p data-preview-title class="mt-1 truncate text-lg text-[#1a0dab] dark:text-[#8ab4f8]"><?= $this->e($seoTitle ?: $post['title'] . ' | ' . option('site_title', '')) ?></p>
        <p data-preview-desc class="mt-0.5 line-clamp-2 text-sm text-gray-600 dark:text-slate-400"><?= $this->e($seoDesc ?: $fallbackDesc) ?></p>
    </div>

    <div>
        <div class="flex items-center justify-between">
            <label class="label" for="seo_title">Título para Google</label>
            <span class="text-xs text-gray-400"><span data-count-title><?= mb_strlen($seoTitle) ?></span>/60</span>
        </div>
        <input id="seo_title" type="text" name="seo_title" value="<?= $this->e($seoTitle) ?>" maxlength="90" placeholder="<?= $this->e($post['title'] . ' | ' . option('site_title', '')) ?>" class="input">
    </div>
    <div>
        <div class="flex items-center justify-between">
            <label class="label" for="seo_description">Descripción para Google</label>
            <span class="text-xs text-gray-400"><span data-count-desc><?= mb_strlen($seoDesc) ?></span>/160</span>
        </div>
        <textarea id="seo_description" name="seo_description" rows="3" maxlength="300" placeholder="<?= $this->e($fallbackDesc) ?>" class="input"><?= $this->e($seoDesc) ?></textarea>
        <p class="hint">Si se deja vacío, se usa el extracto o el inicio del contenido.</p>
    </div>
    <div>
        <label class="label" for="seo_image">Imagen al compartir (URL)</label>
        <input id="seo_image" type="text" name="seo_image" value="<?= $this->e($meta['seo_image'] ?? '') ?>" placeholder="/uploads/…/imagen.jpg" class="input">
    </div>
    <label class="flex cursor-pointer items-center gap-3">
        <input type="checkbox" name="seo_noindex" value="1" <?= ($meta['seo_noindex'] ?? '0') === '1' ? 'checked' : '' ?> class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
        <span class="text-sm text-gray-700 dark:text-slate-300">No mostrar esta página en Google</span>
    </label>
</div>
<script>
(function () {
    var box = document.getElementById('seo-box');
    var title = box.querySelector('#seo_title'), desc = box.querySelector('#seo_description');
    var pt = box.querySelector('[data-preview-title]'), pd = box.querySelector('[data-preview-desc]');
    var ct = box.querySelector('[data-count-title]'), cd = box.querySelector('[data-count-desc]');
    function paint(el, n, max) { el.textContent = n; el.parentElement.style.color = n > max ? '#ef4444' : ''; }
    title.addEventListener('input', function () { pt.textContent = title.value || title.placeholder; paint(ct, title.value.length, 60); });
    desc.addEventListener('input', function () { pd.textContent = desc.value || desc.placeholder; paint(cd, desc.value.length, 160); });
})();
</script>
