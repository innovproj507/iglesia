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

        $html = $this->view->render('admin::' . $template, array_merge($data, [
            'currentUser' => $user,
            'menuItems' => $this->adminMenu->items($user),
            'activeMenu' => $activeMenu,
        ]));

        return Response::html($html);
    }
}
