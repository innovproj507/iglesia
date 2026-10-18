<?php
$href = $href ?? '/' . ($post['slug'] ?? '');
$author = $author ?? ($post['predicador'] ?? '');
$label = $label ?? null;
$date = format_date($post['fecha_servicio'] ?? $post['published_at'] ?? $post['created_at'] ?? '');
$excerpt = $post['excerpt'] ?? strip_tags($post['content'] ?? '');
$excerpt = mb_strimwidth(trim($excerpt), 0, 130, '…');
$image = post_image($post);
?>
<a href="<?= $this->e($href) ?>" class="reveal group flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-100 transition duration-300 hover:-translate-y-1 hover:shadow-xl">
    <div class="relative aspect-[16/10] overflow-hidden bg-brand-100">
        <?php if ($image): ?>
            <img src="<?= $this->e($image) ?>" alt="" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
        <?php else: ?>
            <div class="grid h-full w-full place-items-center bg-gradient-to-br from-brand-500 to-brand-800 p-6 text-center">
                <span class="font-serif text-xl italic text-white/90"><?= $this->e($post['title'] ?? '') ?></span>
            </div>
        <?php endif; ?>
        <?php if ($label): ?>
            <span class="absolute left-4 top-4 rounded-full bg-white/95 px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-brand-700"><?= $this->e($label) ?></span>
        <?php endif; ?>
    </div>
    <div class="flex flex-1 flex-col p-6">
        <p class="mb-2 text-xs font-medium text-gray-400">
            <?= $this->e($date) ?><?php if ($author): ?> · <?= $this->e($author) ?><?php endif; ?>
        </p>
        <h3 class="mb-2 text-lg font-bold leading-snug text-gray-900 group-hover:text-brand-600 transition"><?= $this->e($post['title'] ?? '') ?></h3>
        <p class="mb-5 text-sm leading-relaxed text-gray-500 line-clamp-3"><?= $this->e($excerpt) ?></p>
        <span class="mt-auto inline-flex items-center gap-1 text-sm font-bold text-brand-600">
            Leer <span class="transition group-hover:translate-x-1">→</span>
        </span>
    </div>
</a>
