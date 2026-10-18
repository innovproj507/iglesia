<?php
$videoId = youtube_id($sermon['video_url'] ?? null);
$embedUrl = $videoId ? "https://www.youtube-nocookie.com/embed/{$videoId}" : ($sermon['video_url'] ?? '');
$series = array_map(fn ($s) => $s['name'], $sermon['series'] ?? []);
?>
<?php $this->insert('partials/header', array_merge(get_defined_vars(), ['pageTitle' => $sermon['title']])) ?>
<?php $this->insert('partials/page-header', [
    'eyebrow' => $series ? implode(' · ', $series) : 'Sermón',
    'heading' => $sermon['title'],
    'subheading' => trim(($sermon['predicador'] ?: 'Predicador invitado') . ' · ' . format_date($sermon['fecha_servicio'] ?? ''), ' ·'),
]) ?>

<article class="container-site pb-20">
    <?php if ($embedUrl): ?>
        <div class="mx-auto -mt-10 mb-12 aspect-video max-w-5xl overflow-hidden rounded-3xl bg-gray-900 shadow-2xl ring-1 ring-black/10 sm:-mt-12">
            <iframe src="<?= $this->e($embedUrl) ?>" title="<?= $this->e($sermon['title']) ?>" class="h-full w-full" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
    <?php elseif ($sermon['audio_url']): ?>
        <div class="mx-auto mt-12 mb-12 max-w-3xl rounded-2xl bg-brand-50 p-6">
            <p class="mb-3 text-sm font-bold text-brand-700">Escuchar mensaje</p>
            <audio controls class="w-full"><source src="<?= $this->e($sermon['audio_url']) ?>"></audio>
        </div>
    <?php else: ?>
        <div class="h-12"></div>
    <?php endif; ?>

    <div class="prose prose-lg prose-a:text-brand-600 prose-blockquote:border-brand-400 mx-auto max-w-3xl"><?= $sermon['content'] ?></div>

    <div class="mx-auto mt-16 flex max-w-3xl flex-wrap items-center justify-between gap-4 border-t border-gray-100 pt-8">
        <a href="/sermones" class="font-bold text-brand-600 hover:text-brand-700">← Todos los sermones</a>
        <?php if ($embedUrl && $sermon['audio_url']): ?>
            <audio controls class="w-full sm:w-80"><source src="<?= $this->e($sermon['audio_url']) ?>"></audio>
        <?php endif; ?>
    </div>
</article>

<?php $this->insert('partials/footer', get_defined_vars()) ?>
