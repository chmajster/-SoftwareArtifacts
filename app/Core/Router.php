<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];

    public function add(string $method, string $path, callable $handler): void
    {
        $this->routes[strtoupper($method)][rtrim($path, '/') ?: '/'] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = rtrim(parse_url($uri, PHP_URL_PATH) ?: '/', '/') ?: '/';
        $handler = $this->routes[strtoupper($method)][$path] ?? null;

        if (!$handler) {
            http_response_code(404);
            echo 'Route not found';
            return;
        }

        $handler();
    }
}
