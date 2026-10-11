<?php

namespace Cms\Admin\Controllers;

use Cms\Admin\AdminMenu;
use Cms\Auth\AuthManager;
use Cms\Http\Request;
use Cms\Http\Response;
use Cms\Menus\MenuRepository;
use Cms\Themes\View;

class MenusController extends AdminController
{
    public function __construct(
        View $view,
        AuthManager $auth,
        AdminMenu $adminMenu,
        private MenuRepository $menus,
    ) {
        parent::__construct($view, $auth, $adminMenu);
    }

    public function index(Request $request): Response
    {
        $menus = $this->menus->all();
        $activeMenuId = (int) $request->input('menu_id', $menus[0]['id'] ?? 0);
        $items = $activeMenuId ? $this->menus->items($activeMenuId) : [];

        return $this->render('menus/index', [
            'title' => 'Menús',
            'menus' => $menus,
            'activeMenuId' => $activeMenuId,
            'items' => $items,
        ], 'menus');
    }

    public function store(Request $request): Response
    {
        $this->menus->create(
            (string) $request->input('name', 'Menú'),
            $request->input('location') ?: null
        );

        return Response::redirect('/admin/menus');
    }

    public function addItem(Request $request): Response
    {
        $this->menus->addItem(
            (int) $request->input('menu_id'),
            (string) $request->input('label', ''),
            $request->input('url'),
            null,
            $request->input('parent_id') ?: null,
            (int) $request->input('sort_order', 0)
        );

        return Response::redirect('/admin/menus?menu_id=' . (int) $request->input('menu_id'));
    }

    public function removeItem(Request $request): Response
    {
        $this->menus->removeItem((int) $request->param('id'));

        return Response::redirect('/admin/menus?menu_id=' . (int) $request->input('menu_id'));
    }
}
