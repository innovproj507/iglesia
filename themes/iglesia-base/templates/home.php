<?php $this->insert('partials/header', get_defined_vars()) ?>
<?php $this->insert('partials/hero', get_defined_vars()) ?>

<section class="max-w-6xl mx-auto px-4 py-16">
    <h2 class="text-2xl font-bold text-gray-900 mb-8">Sermones recientes</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach (($recentSermons ?? []) as $sermon): ?>
            <?= $this->fetch('partials/card-post', ['post' => $sermon, 'href' => '/sermones/' . $sermon['slug']]) ?>
        <?php endforeach; ?>
        <?php if (empty($recentSermons)): ?>
            <p class="text-gray-400 col-span-full text-center py-8">Aún no hay sermones publicados.</p>
        <?php endif; ?>
    </div>
</section>

<?php $this->insert('partials/service-times', get_defined_vars()) ?>

<section class="max-w-6xl mx-auto px-4 py-16">
    <h2 class="text-2xl font-bold text-gray-900 mb-8">Últimas entradas</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach (($recentPosts ?? []) as $post): ?>
            <?= $this->fetch('partials/card-post', ['post' => $post, 'href' => '/' . $post['slug']]) ?>
        <?php endforeach; ?>
        <?php if (empty($recentPosts)): ?>
            <p class="text-gray-400 col-span-full text-center py-8">Aún no hay entradas publicadas.</p>
        <?php endif; ?>
    </div>
</section>

<?php $this->insert('partials/footer', get_defined_vars()) ?>
