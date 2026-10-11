<?php $this->insert('partials/header', get_defined_vars()) ?>

<article class="max-w-3xl mx-auto px-4 py-12">
    <p class="text-sm font-medium text-brand-700 mb-2">
        <?php foreach (($sermon['series'] ?? []) as $serie): ?>
            <span class="mr-2"><?= $this->e($serie['name']) ?></span>
        <?php endforeach; ?>
    </p>
    <h1 class="text-3xl font-bold text-gray-900 mb-2"><?= $this->e($sermon['title']) ?></h1>
    <p class="text-sm text-gray-500 mb-6">
        <?= $this->e($sermon['predicador'] ?: 'Predicador invitado') ?>
        <?php if ($sermon['fecha_servicio']): ?> · <?= $this->e($sermon['fecha_servicio']) ?><?php endif; ?>
    </p>

    <?php if ($sermon['video_url']): ?>
        <div class="aspect-video mb-8 rounded-lg overflow-hidden bg-gray-100">
            <iframe src="<?= $this->e($sermon['video_url']) ?>" class="w-full h-full" allowfullscreen></iframe>
        </div>
    <?php elseif ($sermon['audio_url']): ?>
        <audio controls class="w-full mb-8">
            <source src="<?= $this->e($sermon['audio_url']) ?>">
        </audio>
    <?php endif; ?>

    <div class="prose max-w-none"><?= $sermon['content'] ?></div>
</article>

<?php $this->insert('partials/footer', get_defined_vars()) ?>
