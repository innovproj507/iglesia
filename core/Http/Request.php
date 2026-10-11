<?php

namespace Cms\Http;

class Request
{
    public readonly string $method;
    public readonly string $path;
    public readonly array $query;
    public readonly array $body;
    public readonly array $server;
    public readonly array $files;
    private array $routeParams = [];

    public function __construct(string $method, string $path, array $query, array $body, array $server, array $files = [])
    {
        $this->method = $method;
        $this->path = $path;
        $this->query = $query;
        $this->body = $body;
        $this->server = $server;
        $this->files = $files;
    }

    public static function capture(): self
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = rawurldecode(parse_url($uri, PHP_URL_PATH) ?? '/');

        if ($path === '') {
            $path = '/';
        }

        return new self($method, $path, $_GET, $_POST, $_SERVER, $_FILES);
    }

    public function setRouteParams(array $params): void
    {
        $this->routeParams = $params;
    }

    public function param(string $key, mixed $default = null): mixed
    {
        return $this->routeParams[$key] ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $this->query[$key] ?? $default;
    }

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }
}
