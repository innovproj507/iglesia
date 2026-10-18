<?php

namespace Plugins\Mantenimiento\Http;

use Cms\Admin\Controllers\AdminController;
use Cms\Http\Request;
use Cms\Http\Response;
use Plugins\Mantenimiento\Mantenimiento;

class MaintenanceController extends AdminController
{
    public function index(Request $request): Response
    {
        $https = ($_SERVER['HTTPS'] ?? '') === 'on' || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
        $base = ($https ? 'https' : 'http') . '://' . ($_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'] ?? 'localhost');
        $until = (string) option('maint_until', '');

        $flash = match ($request->input('ok')) {
            'on' => 'Modo mantenimiento activado. Los visitantes ahora ven la página de mantenimiento.',
            'off' => 'El sitio vuelve a estar visible para todos.',
            'saved' => 'Ajustes guardados.',
            'token' => 'Se generó un nuevo enlace de acceso. El anterior ya no funciona.',
            default => null,
        };

        return $this->render('mantenimiento::admin/settings', [
            'title' => 'Modo mantenimiento',
            'enabled' => Mantenimiento::enabled(),
            'maintTitle' => (string) option('maint_title', ''),
            'maintMessage' => (string) option('maint_message', ''),
            'maintUntil' => $until !== '' ? date('Y-m-d\TH:i', strtotime($until)) : '',
            'showContact' => option('maint_show_contact') === null ? true : (bool) option('maint_show_contact'),
            'accessUrl' => $base . '/?acceso=' . Mantenimiento::token(),
            'flash' => $flash,
        ], 'mantenimiento');
    }

    public function update(Request $request): Response
    {
        update_option('maint_title', trim((string) $request->input('maint_title', '')));
        update_option('maint_message', trim((string) $request->input('maint_message', '')));
        update_option('maint_until', trim((string) $request->input('maint_until', '')));
        update_option('maint_show_contact', $request->input('maint_show_contact') ? '1' : '0');

        return Response::redirect('/admin/mantenimiento?ok=saved');
    }

    public function toggle(Request $request): Response
    {
        $enable = !Mantenimiento::enabled();
        update_option('maint_enabled', $enable ? '1' : '0');

        return Response::redirect('/admin/mantenimiento?ok=' . ($enable ? 'on' : 'off'));
    }

    public function regenerateToken(Request $request): Response
    {
        update_option('maint_token', '');
        Mantenimiento::token();

        return Response::redirect('/admin/mantenimiento?ok=token');
    }

    public function preview(Request $request): Response
    {
        return Mantenimiento::page(200);
    }
}
