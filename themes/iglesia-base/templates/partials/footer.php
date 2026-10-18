</main>

    <?php do_action('front.footer') ?>

    <footer class="relative bg-brand-950 text-brand-100/80">
        <div class="h-1 bg-gradient-to-r from-brand-500 via-brand-300 to-earth-400"></div>
        <div class="container-site grid grid-cols-1 gap-12 py-16 md:grid-cols-2 lg:grid-cols-4">
            <div class="lg:col-span-2 max-w-md">
                <?php if ($logo = option('logo')): ?>
                    <a href="/" aria-label="<?= $this->e($siteTitle ?? 'Inicio') ?>">
                        <img src="<?= $this->e($logo) ?>" alt="<?= $this->e($siteTitle ?? '') ?>" class="h-14 w-auto brightness-0 invert">
                    </a>
                <?php else: ?>
                <a href="/" class="flex items-center gap-3">
                    <span class="grid place-items-center h-11 w-11 rounded-xl bg-brand-500 text-white">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 3v18M6 9h12"/></svg>
                    </span>
                    <span class="text-xl font-extrabold text-white"><?= $this->e($siteTitle ?? 'Iglesia') ?></span>
                </a>
                <?php endif; ?>
                <p class="mt-5 text-sm leading-relaxed">
                    <?= $this->e($tagline ?: 'Una comunidad que ama a Dios, ama a las personas y sirve a su ciudad.') ?>
                </p>
                <div class="mt-6 flex items-center gap-4">
                    <?php $this->insert('partials/social-icons', ['socialLinks' => $socialLinks ?? [], 'size' => 'h-5 w-5', 'class' => 'grid place-items-center h-10 w-10 rounded-full bg-white/5 text-brand-100 hover:bg-brand-500 hover:text-white']) ?>
                </div>
            </div>

            <div>
                <h4 class="text-sm font-bold uppercase tracking-widest text-white mb-5">Enlaces</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="/" class="hover:text-white transition">Inicio</a></li>
                    <?php foreach (($footerMenuItems ?? []) as $item): ?>
                        <li><a href="<?= $this->e($item['url'] ?: '#') ?>" class="hover:text-white transition"><?= $this->e($item['label']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
                <h4 class="text-sm font-bold uppercase tracking-widest text-white mt-8 mb-5">Horarios</h4>
                <ul class="space-y-3 text-sm">
                    <?php foreach (service_schedule() as $item): ?>
                        <li>
                            <span class="block font-semibold text-white/90"><?= $this->e($item['title']) ?></span>
                            <?= $this->e(trim($item['time'] . ($item['day'] ? ' · ' . $item['day'] : ''))) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div>
                <h4 class="text-sm font-bold uppercase tracking-widest text-white mb-5">Contacto</h4>
                <ul class="space-y-4 text-sm">
                    <?php if ($address = option('address')): ?>
                        <li><a href="<?= $this->e(maps_url()) ?>" target="_blank" rel="noopener" class="hover:text-white"><?= $this->e($address) ?></a></li>
                    <?php endif; ?>
                    <?php if ($phone = option('phone')): ?>
                        <li><a href="tel:<?= $this->e(preg_replace('/[^\d+]/', '', $phone)) ?>" class="hover:text-white"><?= $this->e($phone) ?></a></li>
                    <?php endif; ?>
                    <?php if ($email = option('email')): ?>
                        <li><a href="mailto:<?= $this->e($email) ?>" class="break-all hover:text-white"><?= $this->e($email) ?></a></li>
                    <?php endif; ?>
                    <?php if ($wa = whatsapp_url()): ?>
                        <li>
                            <a href="<?= $this->e($wa) ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 font-semibold text-white hover:bg-brand-500">
                                <?php $this->insert('partials/whatsapp-icon', ['class' => 'h-4 w-4']) ?> WhatsApp
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <div class="border-t border-white/10">
            <div class="container-site flex flex-col items-center justify-between gap-2 py-6 text-xs text-brand-100/60 sm:flex-row">
                <p>&copy; <?= date('Y') ?> <?= $this->e($siteTitle ?? 'Iglesia') ?>. Todos los derechos reservados.</p>
                <a href="#contenido" class="hover:text-white">Volver arriba ↑</a>
            </div>
        </div>
    </footer>

    <?php if ($wa = whatsapp_url()): ?>
        <a href="<?= $this->e($wa) ?>" target="_blank" rel="noopener" aria-label="Escríbenos por WhatsApp"
           class="fixed bottom-5 right-5 z-50 grid h-14 w-14 place-items-center rounded-full bg-[#25D366] text-white shadow-xl shadow-black/20 transition hover:scale-110">
            <?php $this->insert('partials/whatsapp-icon', ['class' => 'h-7 w-7']) ?>
        </a>
    <?php endif; ?>

    <script src="/assets/theme/app.js" defer></script>
</body>
</html>
