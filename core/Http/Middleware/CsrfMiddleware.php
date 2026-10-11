<?php

namespace Cms\Http\Middleware;

use Cms\Http\Request;
use Cms\Http\Response;
use Cms\Support\Csrf;

class CsrfMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        if ($request->isPost() && !Csrf::verify($request->input('_token'))) {
            return new Response('Invalid CSRF token', 419);
        }

        return $next($request);
    }
}
