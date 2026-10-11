<?php
$href = $href ?? '/' . ($post['slug'] ?? '');
$author = $author ?? '';
$date = $post['fecha_servicio'] ?? $post['published_at'] ?? $post['created_at'] ?? '';
$excerpt = $post['excerpt'] ?? strip_tags($post['content'] ?? '');
$excerpt = mb_strimwidth(trim($excerpt), 0, 140, '…');
?>
<a href="<?= $this->e($href) ?>" class="block bg-white border border-gray-100 rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden">
    <div class="h-40 bg-gray-100 flex items-center justify-center text-gray-300 text-sm">
        <?= $this->e($post['title'] ?? '') ?>
    </div>
    <div class="p-5">
        <h3 class="font-semibold text-gray-900 mb-1"><?= $this->e($post['title'] ?? '') ?></h3>
        <p class="text-xs text-gray-400 mb-3">
            <?php if ($author): ?><?= $this->e($author) ?> · <?php endif; ?>
            <?= $this->e($date) ?>
        </p>
        <p class="text-sm text-gray-600 mb-3"><?= $this->e($excerpt) ?></p>
        <span class="text-sm font-medium text-brand-700">Leer más →</span>
    </div>
</a>
