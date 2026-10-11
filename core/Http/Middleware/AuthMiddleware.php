<?php

namespace Cms\Http\Middleware;

use Cms\Auth\AuthManager;
use Cms\Http\Request;
use Cms\Http\Response;

class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(private AuthManager $auth)
    {
    }

    public function handle(Request $request, callable $next): Response
    {
        if (!$this->auth->check()) {
            return Response::redirect('/admin/login');
        }

        return $next($request);
    }
}
