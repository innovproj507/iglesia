<?php

namespace Cms\Admin\Controllers;

use Cms\Admin\AdminMenu;
use Cms\Auth\AuthManager;
use Cms\Http\Request;
use Cms\Http\Response;
use Cms\Plugins\PluginManager;
use Cms\Themes\View;

class PluginsController extends AdminController
{
    public function __construct(
        View $view,
        AuthManager $auth,
        AdminMenu $adminMenu,
        private PluginManager $plugins,
    ) {
        parent::__construct($view, $auth, $adminMenu);
    }

    public function index(Request $request): Response
    {
        $manifests = $this->plugins->discover();
        $activeSlugs = $this->plugins->getActiveSlugs();

        return $this->render('plugins/index', [
            'title' => 'Plugins',
            'manifests' => $manifests,
            'activeSlugs' => $activeSlugs,
        ], 'plugins');
    }

    public function activate(Request $request): Response
    {
        $this->plugins->activate((string) $request->param('slug'));

        return Response::redirect('/admin/plugins');
    }

    public function deactivate(Request $request): Response
    {
        $this->plugins->deactivate((string) $request->param('slug'));

        return Response::redirect('/admin/plugins');
    }
}
