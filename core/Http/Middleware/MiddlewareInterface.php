<?php

namespace Cms\Http\Middleware;

use Cms\Http\Request;
use Cms\Http\Response;

interface MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response;
}
