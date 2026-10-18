<?php $this->insert('partials/header', array_merge(get_defined_vars(), ['pageTitle' => 'Noticias'])) ?>
<?php $this->insert('partials/page-header', ['eyebrow' => 'Blog', 'heading' => 'Noticias y reflexiones', 'subheading' => 'Lo que Dios está haciendo en nuestra comunidad.']) ?>

<div class="container-site py-20">
    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach (($posts ?? []) as $post): ?>
            <?= $this->fetch('partials/card-post', ['post' => $post, 'href' => '/' . $post['slug']]) ?>
        <?php endforeach; ?>
        <?php if (empty($posts)): ?>
            <p class="col-span-full py-12 text-center text-gray-400">Aún no hay entradas publicadas.</p>
        <?php endif; ?>
    </div>
</div>

<?php $this->insert('partials/footer', get_defined_vars()) ?>
