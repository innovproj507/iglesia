<?php

namespace Cms\Admin\Controllers;

use Cms\Database\Connection;
use Cms\Http\Request;
use Cms\Http\Response;

class DashboardController extends AdminController
{
    public function __construct(
        \Cms\Themes\View $view,
        \Cms\Auth\AuthManager $auth,
        \Cms\Admin\AdminMenu $adminMenu,
        private Connection $connection,
    ) {
        parent::__construct($view, $auth, $adminMenu);
    }

    public function index(Request $request): Response
    {
        $widgets = [];
        do_action('admin.dashboard.widgets', $widgets);

        return $this->render('dashboard', [
            'title' => 'Dashboard',
            'postCount' => $this->connection->count('posts', ['post_type' => 'post']),
            'pageCount' => $this->connection->count('posts', ['post_type' => 'page']),
            'mediaCount' => $this->connection->count('media'),
            'userCount' => $this->connection->count('users'),
        ], 'dashboard');
    }
}
