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
    <p class="text-sm text-gray-500 mb-6">Paso 2 de 4 — Conexión a la base de datos</p>

    <?php if ($error): ?>
        <div class="mb-4 rounded bg-red-50 border border-red-300 text-red-700 px-4 py-2 text-sm">
            <?= $this->e($error) ?>
        </div>
    <?php endif; ?>

    <form method="post" action="/install/database" class="space-y-3">
        <?= csrf_field() ?>
        <div>
            <label class="block text-sm font-medium mb-1">Host</label>
            <input type="text" name="host" value="127.0.0.1" required class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Puerto</label>
            <input type="text" name="port" value="3306" required class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Base de datos</label>
            <input type="text" name="database" value="iglesia" required class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Usuario</label>
            <input type="text" name="username" value="root" required class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Contraseña</label>
            <input type="password" name="password" class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <button type="submit" class="w-full bg-brand-700 text-white rounded py-2 text-sm font-medium hover:bg-brand-800">
            Probar conexión y continuar
        </button>
    </form>
</div>
</body>
</html>
