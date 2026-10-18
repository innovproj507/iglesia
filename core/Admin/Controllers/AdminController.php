<?php

namespace Cms\Admin\Controllers;

use Cms\Admin\AdminMenu;
use Cms\Auth\AuthManager;
use Cms\Http\Response;
use Cms\Themes\View;

abstract class AdminController
{
    public function __construct(
        protected View $view,
        protected AuthManager $auth,
        protected AdminMenu $adminMenu,
    ) {
    }

    protected function render(string $template, array $data = [], string $activeMenu = ''): Response
    {
        $user = $this->auth->user();

        // Los plugins pueden pasar su propia vista con espacio de nombres (ej. "seo::admin/settings").
        $name = str_contains($template, '::') ? $template : 'admin::' . $template;

        $html = $this->view->render($name, array_merge($data, [
            'currentUser' => $user,
            'menuItems' => $this->adminMenu->items($user),
            'activeMenu' => $activeMenu,
        ]));

        return Response::html($html);
    }
}
