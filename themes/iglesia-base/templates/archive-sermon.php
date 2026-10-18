<?php $this->insert('partials/header', array_merge(get_defined_vars(), ['pageTitle' => 'Sermones'])) ?>
<?php $this->insert('partials/page-header', ['eyebrow' => 'Crece', 'heading' => 'Sermones', 'subheading' => 'Mensajes de la Palabra de Dios para tu vida diaria.']) ?>

<div class="container-site py-20">
    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach (($sermons ?? []) as $sermon): ?>
            <?= $this->fetch('partials/card-post', [
                'post' => $sermon,
                'href' => '/sermones/' . $sermon['slug'],
                'author' => $sermon['predicador'],
                'label' => $sermon['series'][0]['name'] ?? null,
            ]) ?>
        <?php endforeach; ?>
        <?php if (empty($sermons)): ?>
            <p class="col-span-full py-12 text-center text-gray-400">Aún no hay sermones publicados.</p>
        <?php endif; ?>
    </div>
</div>

<?php $this->insert('partials/footer', get_defined_vars()) ?>
