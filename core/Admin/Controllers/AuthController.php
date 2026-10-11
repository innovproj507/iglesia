<?php

namespace Cms\Admin\Controllers;

use Cms\Auth\AuthManager;
use Cms\Http\Request;
use Cms\Http\Response;
use Cms\Themes\View;

class AuthController
{
    public function __construct(private View $view, private AuthManager $auth)
    {
    }

    public function showLogin(Request $request): Response
    {
        if ($this->auth->check()) {
            return Response::redirect('/admin');
        }

        return Response::html($this->view->render('admin::auth/login', ['error' => null]));
    }

    public function login(Request $request): Response
    {
        $email = (string) $request->input('email', '');
        $password = (string) $request->input('password', '');

        if (!$this->auth->attempt($email, $password)) {
            return Response::html($this->view->render('admin::auth/login', [
                'error' => 'Credenciales inválidas.',
            ]));
        }

        return Response::redirect('/admin');
    }

    public function logout(Request $request): Response
    {
        $this->auth->logout();

        return Response::redirect('/admin/login');
    }
}
