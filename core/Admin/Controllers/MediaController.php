<?php

namespace Cms\Admin\Controllers;

use Cms\Admin\AdminMenu;
use Cms\Auth\AuthManager;
use Cms\Http\Request;
use Cms\Http\Response;
use Cms\Media\MediaLibrary;
use Cms\Themes\View;

class MediaController extends AdminController
{
    public function __construct(
        View $view,
        AuthManager $auth,
        AdminMenu $adminMenu,
        private MediaLibrary $media,
    ) {
        parent::__construct($view, $auth, $adminMenu);
    }

    public function index(Request $request): Response
    {
        $result = $this->media->paginate((int) $request->input('page', 1));

        return $this->render('media/index', [
            'title' => 'Media',
            'items' => $result['items'],
        ], 'media');
    }

    public function store(Request $request): Response
    {
        $file = $request->file('file');

        if ($file) {
            $this->media->upload($file, $this->auth->user()?->id, $request->input('alt_text'));
        }

        return Response::redirect('/admin/media');
    }

    public function destroy(Request $request): Response
    {
        $this->media->delete((int) $request->param('id'), base_path('public/uploads'));

        return Response::redirect('/admin/media');
    }
}
