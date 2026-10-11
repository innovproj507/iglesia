<?php

namespace Cms\Http;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

use function FastRoute\simpleDispatcher;

class Router
{
    /** @var array<int, array{method: string, path: string, handler: mixed, middleware: array}> */
    private array $routes = [];

    private array $groupMiddleware = [];
    private string $groupPrefix = '';

    public function get(string $path, mixed $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post(string $path, mixed $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    public function any(array $methods, string $path, mixed $handler, array $middleware = []): void
    {
        foreach ($methods as $method) {
            $this->addRoute(strtoupper($method), $path, $handler, $middleware);
        }
    }

    public function group(string $prefix, callable $callback, array $middleware = []): void
    {
        $previousPrefix = $this->groupPrefix;
        $previousMiddleware = $this->groupMiddleware;

        $this->groupPrefix = $previousPrefix . $prefix;
        $this->groupMiddleware = array_merge($previousMiddleware, $middleware);

        $callback($this);

        $this->groupPrefix = $previousPrefix;
        $this->groupMiddleware = $previousMiddleware;
    }

    private function addRoute(string $method, string $path, mixed $handler, array $middleware): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $this->groupPrefix . $path,
            'handler' => $handler,
            'middleware' => array_merge($this->groupMiddleware, $middleware),
        ];
    }

    public function dispatch(Request $request): Response
    {
        $dispatcher = simpleDispatcher(function (RouteCollector $r) {
            foreach ($this->routes as $route) {
                $r->addRoute($route['method'], $route['path'], $route);
            }
        });

        $routeInfo = $dispatcher->dispatch($request->method, $request->path);

        return match ($routeInfo[0]) {
            Dispatcher::NOT_FOUND => Response::notFound(),
            Dispatcher::METHOD_NOT_ALLOWED => new Response('Method Not Allowed', 405),
            Dispatcher::FOUND => $this->handleFound($routeInfo[1], $routeInfo[2], $request),
            default => Response::notFound(),
        };
    }

    private function handleFound(array $route, array $vars, Request $request): Response
    {
        $request->setRouteParams($vars);

        $pipeline = array_reduce(
            array_reverse($route['middleware']),
            fn (callable $next, string|array $middleware) => fn (Request $req) => $this->resolveMiddleware($middleware)->handle($req, $next),
            fn (Request $req) => $this->callHandler($route['handler'], $req)
        );

        return $pipeline($request);
    }

    private function resolveMiddleware(string|array $middleware): object
    {
        [$class, $param] = is_array($middleware) ? $middleware : [$middleware, null];
        $instance = app($class);

        if ($param !== null && method_exists($instance, 'withParam')) {
            $instance = $instance->withParam($param);
        }

        return $instance;
    }

    private function callHandler(mixed $handler, Request $request): Response
    {
        if (is_array($handler) && is_string($handler[0])) {
            $controller = app($handler[0]);
            $method = $handler[1];

            return $controller->$method($request);
        }

        if (is_callable($handler)) {
            return $handler($request);
        }

        throw new \RuntimeException('Invalid route handler.');
    }
}
