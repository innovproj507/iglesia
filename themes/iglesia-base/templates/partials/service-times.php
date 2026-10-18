<?php
$address = option('address');
$schedule = service_schedule();
$mapsUrl = maps_url();
$whatsappUrl = whatsapp_url();
$phone = option('phone');
$scheduleNote = option('schedule_note');
?>
<section id="horarios" class="relative isolate overflow-hidden bg-brand-800 py-24 text-white scroll-mt-24">
    <div class="absolute -right-32 -top-32 -z-10 h-96 w-96 rounded-full bg-brand-500/30 blur-3xl"></div>
    <div class="absolute -bottom-32 -left-32 -z-10 h-96 w-96 rounded-full bg-earth-500/20 blur-3xl"></div>
    <span id="visitanos" class="absolute -top-24"></span>

    <div class="container-site grid items-center gap-12 lg:grid-cols-5">
        <div class="reveal lg:col-span-2">
            <span class="eyebrow text-brand-300">Nuestros horarios</span>
            <h2 class="mt-3 text-3xl font-bold tracking-tight sm:text-5xl">Hay un lugar para ti este domingo</h2>
            <p class="mt-5 max-w-lg text-lg text-brand-100/90">
                <?= $this->e($scheduleNote ?: 'Ven tal como eres. Te recibiremos con los brazos abiertos y un mensaje de la Biblia para tu vida diaria.') ?>
            </p>
            <?php if ($address): ?>
                <p class="mt-6 flex items-start gap-3 text-brand-50">
                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-brand-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.14-7.5 11.25-7.5 11.25S4.5 17.64 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                    <?= $this->e($address) ?>
                </p>
            <?php endif; ?>
            <div class="mt-8 flex flex-wrap gap-3">
                <?php if ($mapsUrl): ?>
                    <a href="<?= $this->e($mapsUrl) ?>" target="_blank" rel="noopener" class="btn bg-white text-brand-800 hover:bg-brand-50">Cómo llegar</a>
                <?php endif; ?>
                <?php if ($whatsappUrl): ?>
                    <a href="<?= $this->e($whatsappUrl) ?>" target="_blank" rel="noopener" class="btn-outline">
                        <?php $this->insert('partials/whatsapp-icon', ['class' => 'h-4 w-4']) ?> WhatsApp
                    </a>
                <?php elseif ($phone): ?>
                    <a href="tel:<?= $this->e(preg_replace('/[^\d+]/', '', $phone)) ?>" class="btn-outline">Llámanos</a>
                <?php endif; ?>
            </div>
        </div>

        <ul class="grid gap-4 sm:grid-cols-2 lg:col-span-3 <?= count($schedule) === 3 ? 'lg:grid-cols-3' : '' ?>">
            <?php foreach ($schedule as $i => $item): ?>
                <li class="reveal flex flex-col rounded-2xl border border-white/10 bg-white/10 p-6 backdrop-blur transition hover:bg-white/15" style="transition-delay: <?= $i * 100 ?>ms">
                    <svg class="mb-5 h-8 w-8 text-brand-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    <p class="text-xs font-bold uppercase tracking-widest text-brand-200"><?= $this->e($item['title']) ?></p>
                    <?php if ($item['time']): ?>
                        <p class="mt-2 text-2xl font-bold leading-tight"><?= $this->e($item['time']) ?></p>
                    <?php endif; ?>
                    <?php if ($item['day']): ?>
                        <p class="mt-auto pt-3 text-sm text-brand-100/80"><?= $this->e($item['day']) ?></p>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
            <?php if (empty($schedule)): ?>
                <li class="text-brand-100/70">Horarios por confirmar.</li>
            <?php endif; ?>
        </ul>
    </div>
</section>
