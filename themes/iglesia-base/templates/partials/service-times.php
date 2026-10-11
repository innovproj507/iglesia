<section class="bg-brand-50 py-16">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-2xl font-bold text-brand-900 mb-6">Horarios de servicio</h2>
        <ul class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <?php foreach (($serviceTimes ?? []) as $time): ?>
                <li class="bg-white px-6 py-3 rounded-lg shadow-sm text-brand-800 font-medium">
                    <?= $this->e($time) ?>
                </li>
            <?php endforeach; ?>
            <?php if (empty($serviceTimes)): ?>
                <li class="text-gray-400">Horarios por confirmar.</li>
            <?php endif; ?>
        </ul>
    </div>
</section>
