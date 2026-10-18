<?php $this->insert('partials/header', array_merge(get_defined_vars(), ['pageTitle' => $post['title']])) ?>
<?php $this->insert('partials/page-header', [
    'eyebrow' => format_date($post['published_at'] ?? $post['created_at'] ?? ''),
    'heading' => $post['title'],
]) ?>

<article class="container-site py-16">
    <?php if ($image = media_url(isset($post['featured_media_id']) ? (int) $post['featured_media_id'] : null)): ?>
        <img src="<?= $this->e($image) ?>" alt="" class="mx-auto -mt-28 mb-12 aspect-video w-full max-w-4xl rounded-3xl object-cover shadow-2xl">
    <?php endif; ?>
    <div class="prose prose-lg prose-headings:font-bold prose-a:text-brand-600 prose-blockquote:border-brand-400 mx-auto max-w-3xl"><?= $post['content'] ?></div>
    <div class="mx-auto mt-16 max-w-3xl border-t border-gray-100 pt-8">
        <a href="/blog" class="font-bold text-brand-600 hover:text-brand-700">← Volver a noticias</a>
    </div>
</article>

<?php $this->insert('partials/footer', get_defined_vars()) ?>
