<?php
$address = option('address');
$phone = option('phone');
$email = option('email');
$whatsappUrl = whatsapp_url();
$mapsUrl = maps_url();
$mapQuery = option('map_query') ?: $address;
$cards = array_filter([
    $address ? ['Ubicación', $address, $mapsUrl, 'Cómo llegar', 'M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM19.5 10.5c0 7.14-7.5 11.25-7.5 11.25S4.5 17.64 4.5 10.5a7.5 7.5 0 1 1 15 0Z'] : null,
    $phone ? ['Teléfono', $phone, 'tel:' . preg_replace('/[^\d+]/', '', $phone), 'Llamar ahora', 'M2.25 6.75c0 8.28 6.72 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.37c0-.52-.35-.97-.85-1.09l-4.42-1.1c-.44-.12-.9.05-1.17.41l-.97 1.29a1.13 1.13 0 0 1-1.21.38 12.04 12.04 0 0 1-7.14-7.14 1.13 1.13 0 0 1 .38-1.21l1.3-.97c.36-.27.52-.73.4-1.17l-1.1-4.42A1.13 1.13 0 0 0 5.37 2.25H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z'] : null,
    $email ? ['Correo electrónico', $email, 'mailto:' . $email, 'Escribir correo', 'M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.24a2.25 2.25 0 0 1-1.07 1.92l-7.5 4.61a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.91V6.75'] : null,
]);
?>
<?php $this->insert('partials/header', array_merge(get_defined_vars(), ['pageTitle' => $post['title']])) ?>
<?php $this->insert('partials/page-header', ['eyebrow' => 'Estamos para ti', 'heading' => $post['title'], 'subheading' => $post['excerpt'] ?? '']) ?>

<section class="container-site relative -mt-10 pb-8">
    <div class="grid gap-6 md:grid-cols-3">
        <?php foreach ($cards as $i => [$label, $value, $href, $cta, $icon]): ?>
            <a href="<?= $this->e($href) ?>" <?= str_starts_with($href, 'http') ? 'target="_blank" rel="noopener"' : '' ?>
               class="reveal group flex flex-col rounded-3xl bg-white p-8 shadow-xl ring-1 ring-gray-100 transition hover:-translate-y-1" style="transition-delay: <?= $i * 100 ?>ms">
                <span class="mb-5 grid h-14 w-14 place-items-center rounded-2xl bg-brand-50 text-brand-600 transition group-hover:bg-brand-500 group-hover:text-white">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="<?= $icon ?>"/></svg>
                </span>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400"><?= $this->e($label) ?></p>
                <p class="mt-2 break-words text-lg font-bold text-gray-900"><?= $this->e($value) ?></p>
                <span class="mt-auto pt-5 text-sm font-bold text-brand-600"><?= $this->e($cta) ?> →</span>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="container-site grid gap-10 py-16 lg:grid-cols-5">
    <div class="reveal lg:col-span-2">
        <?php if (trim(strip_tags($post['content'] ?? '')) !== ''): ?>
            <div class="prose prose-lg prose-a:text-brand-600 mb-10"><?= $post['content'] ?></div>
        <?php endif; ?>

        <h2 class="text-2xl font-bold text-gray-900">Horario de actividades</h2>
        <ul class="mt-6 divide-y divide-gray-100 rounded-2xl bg-brand-50/60 p-2">
            <?php foreach (service_schedule() as $item): ?>
                <li class="flex items-center justify-between gap-4 px-4 py-4">
                    <span>
                        <span class="block font-bold text-gray-900"><?= $this->e($item['title']) ?></span>
                        <span class="text-sm text-gray-500"><?= $this->e($item['day']) ?></span>
                    </span>
                    <span class="shrink-0 rounded-full bg-white px-3 py-1 text-sm font-bold text-brand-700 shadow-sm"><?= $this->e($item['time']) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php if ($whatsappUrl): ?>
            <a href="<?= $this->e($whatsappUrl) ?>" target="_blank" rel="noopener" class="btn mt-8 w-full bg-[#25D366] text-white hover:brightness-95 sm:w-auto">
                <?php $this->insert('partials/whatsapp-icon', ['class' => 'h-5 w-5']) ?> Escríbenos por WhatsApp
            </a>
        <?php endif; ?>
    </div>

    <?php if ($mapQuery): ?>
        <div class="reveal min-h-[420px] overflow-hidden rounded-3xl shadow-xl ring-1 ring-gray-100 lg:col-span-3">
            <iframe src="https://maps.google.com/maps?q=<?= rawurlencode($mapQuery) ?>&z=16&output=embed"
                    title="Mapa: <?= $this->e($address ?? '') ?>" class="h-full min-h-[420px] w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    <?php endif; ?>
</section>

<?php $this->insert('partials/footer', get_defined_vars()) ?>
