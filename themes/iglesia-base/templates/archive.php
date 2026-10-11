<?php $this->insert('partials/header', get_defined_vars()) ?>

<div class="max-w-6xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Blog</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach (($posts ?? []) as $post): ?>
            <?= $this->fetch('partials/card-post', ['post' => $post, 'href' => '/' . $post['slug']]) ?>
        <?php endforeach; ?>
        <?php if (empty($posts)): ?>
            <p class="text-gray-400 col-span-full text-center py-12">Aún no hay entradas publicadas.</p>
        <?php endif; ?>
    </div>
</div>

<?php $this->insert('partials/footer', get_defined_vars()) ?>
