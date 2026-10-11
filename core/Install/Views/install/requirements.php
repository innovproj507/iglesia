<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalación · Iglesia CMS</title>
    <link rel="stylesheet" href="/assets/admin/app.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
<div class="bg-white shadow rounded-lg p-8 w-full max-w-lg">
    <h1 class="text-xl font-semibold mb-1">Instalación</h1>
    <p class="text-sm text-gray-500 mb-6">Paso 1 de 4 — Requisitos del servidor</p>

    <ul class="space-y-2 mb-6">
        <?php foreach ($requirements as $label => $ok): ?>
            <li class="flex items-center justify-between text-sm border rounded px-3 py-2">
                <span><?= $this->e($label) ?></span>
                <span class="<?= $ok ? 'text-green-600' : 'text-red-600' ?>"><?= $ok ? 'OK' : 'Falta' ?></span>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php $allOk = !in_array(false, $requirements, true); ?>

    <?php if ($allOk): ?>
        <form method="post" action="/install/advance/database">
            <button type="submit" class="w-full bg-brand-700 text-white rounded py-2 text-sm font-medium hover:bg-brand-800">
                Continuar
            </button>
        </form>
    <?php else: ?>
        <p class="text-sm text-red-600">Corrige los requisitos marcados como "Falta" antes de continuar.</p>
    <?php endif; ?>
</div>
</body>
</html>
