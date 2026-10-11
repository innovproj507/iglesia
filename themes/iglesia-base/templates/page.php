<?php $this->insert('partials/header', get_defined_vars()) ?>

<article class="max-w-3xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-6"><?= $this->e($post['title']) ?></h1>
    <div class="prose max-w-none"><?= $post['content'] ?></div>
</article>

<?php $this->insert('partials/footer', get_defined_vars()) ?>
