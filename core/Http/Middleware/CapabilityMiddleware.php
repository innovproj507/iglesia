<?php

namespace Cms\Http\Middleware;

use Cms\Auth\AuthManager;
use Cms\Http\Request;
use Cms\Http\Response;

class CapabilityMiddleware implements MiddlewareInterface
{
    private ?string $capability = null;

    public function __construct(private AuthManager $auth)
    {
    }

    public function withParam(string $capability): self
    {
        $clone = clone $this;
        $clone->capability = $capability;

        return $clone;
    }

    public function handle(Request $request, callable $next): Response
    {
        $user = $this->auth->user();

        if (!$user || ($this->capability && !$user->can($this->capability))) {
            return new Response('Forbidden', 403);
        }

        return $next($request);
    }
}
