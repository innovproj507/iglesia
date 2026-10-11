    <?php do_action('front.footer') ?>

    <footer class="bg-gray-900 text-gray-300 mt-16">
        <div class="max-w-6xl mx-auto px-4 py-12 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-white font-semibold mb-2"><?= $this->e($siteTitle ?? 'Iglesia') ?></h3>
                <p class="text-sm text-gray-400"><?= $this->e($tagline ?? '') ?></p>
            </div>
            <div>
                <h4 class="text-white text-sm font-semibold mb-3">Enlaces</h4>
                <ul class="space-y-2">
                    <?php foreach (($footerMenuItems ?? []) as $item): ?>
                        <li><a href="<?= $this->e($item['url'] ?: '#') ?>" class="text-sm text-gray-400 hover:text-white"><?= $this->e($item['label']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div>
                <h4 class="text-white text-sm font-semibold mb-3">Síguenos</h4>
                <div class="flex gap-4">
                    <?php foreach (($socialLinks ?? []) as $network => $url): ?>
                        <a href="<?= $this->e($url) ?>" class="text-sm text-gray-400 hover:text-white capitalize"><?= $this->e($network) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-800 py-4 text-center text-xs text-gray-500">
            &copy; <?= date('Y') ?> <?= $this->e($siteTitle ?? 'Iglesia') ?>. Todos los derechos reservados.
        </div>
    </footer>

    <script src="/assets/theme/app.js" defer></script>
</body>
</html>
