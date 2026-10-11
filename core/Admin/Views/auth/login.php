<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión · Admin</title>
    <link rel="stylesheet" href="/assets/admin/app.css">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white shadow rounded-lg p-8 w-full max-w-sm">
        <h1 class="text-xl font-semibold mb-6 text-center">Panel de administración</h1>
        <?php if ($error): ?>
            <div class="mb-4 rounded bg-red-50 border border-red-300 text-red-700 px-4 py-2 text-sm">
                <?= $this->e($error) ?>
            </div>
        <?php endif; ?>
        <form method="post" action="/admin/login" class="space-y-4">
            <?= csrf_field() ?>
            <div>
                <label class="block text-sm font-medium mb-1">Correo</label>
                <input type="email" name="email" required class="w-full border rounded px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Contraseña</label>
                <input type="password" name="password" required class="w-full border rounded px-3 py-2 text-sm">
            </div>
            <button type="submit" class="w-full bg-brand-700 text-white rounded py-2 text-sm font-medium hover:bg-brand-800">
                Ingresar
            </button>
        </form>
    </div>
</body>
</html>
