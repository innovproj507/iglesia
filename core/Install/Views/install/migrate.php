<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalación · Iglesia CMS</title>
    <link rel="stylesheet" href="/assets/admin/app.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
<div class="bg-white shadow rounded-lg p-8 w-full max-w-lg text-center">
    <h1 class="text-xl font-semibold mb-1">Instalación</h1>
    <p class="text-sm text-gray-500 mb-6">Paso 3 de 4 — Crear tablas y datos iniciales</p>
    <p class="text-sm text-gray-600 mb-6">
        Se crearán las tablas del núcleo (usuarios, roles, contenido, medios, menús, opciones)
        y se sembrarán los roles, capacidades y ajustes por defecto.
    </p>
    <form method="post" action="/install/migrate">
        <?= csrf_field() ?>
        <button type="submit" class="w-full bg-brand-700 text-white rounded py-2 text-sm font-medium hover:bg-brand-800">
            Ejecutar migraciones
        </button>
    </form>
</div>
</body>
</html>
