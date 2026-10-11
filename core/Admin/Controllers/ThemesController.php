<?php

namespace Cms\Admin\Controllers;

use Cms\Admin\AdminMenu;
use Cms\Auth\AuthManager;
use Cms\Http\Request;
use Cms\Http\Response;
use Cms\Settings\OptionsRepository;
use Cms\Themes\ThemeManager;
use Cms\Themes\View;

class ThemesController extends AdminController
{
    public function __construct(
        View $view,
        AuthManager $auth,
        AdminMenu $adminMenu,
        private ThemeManager $themes,
        private OptionsRepository $options,
    ) {
        parent::__construct($view, $auth, $adminMenu);
    }

    public function index(Request $request): Response
    {
        return $this->render('themes/index', [
            'title' => 'Temas',
            'themes' => $this->themes->discover(),
            'activeSlug' => $this->themes->activeSlug(),
        ], 'themes');
    }

    public function activate(Request $request): Response
    {
        $this->options->set('active_theme', (string) $request->param('slug'));

        return Response::redirect('/admin/themes');
    }
}
