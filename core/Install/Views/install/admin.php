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
    <p class="text-sm text-gray-500 mb-6">Paso 4 de 4 — Usuario administrador</p>

    <form method="post" action="/install/admin" class="space-y-3">
        <?= csrf_field() ?>
        <div>
            <label class="block text-sm font-medium mb-1">Nombre</label>
            <input type="text" name="name" required class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Correo</label>
            <input type="email" name="email" required class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Contraseña</label>
            <input type="password" name="password" required minlength="8" class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <button type="submit" class="w-full bg-brand-700 text-white rounded py-2 text-sm font-medium hover:bg-brand-800">
            Finalizar instalación
        </button>
    </form>
</div>
</body>
</html>
