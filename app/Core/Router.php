<?php
namespace App\Core;

class Router
{
    private array $routes = [];

    public function add(string $method, string $page, string $controller, string $action): void
    {
        $this->routes[strtoupper($method)][$page] = [$controller, $action];
    }

    public function get(string $page, string $controller, string $action): void
    {
        $this->add('GET', $page, $controller, $action);
    }

    public function post(string $page, string $controller, string $action): void
    {
        $this->add('POST', $page, $controller, $action);
    }

    public function dispatch(\PDO $db): void
    {
        $page   = trim($_GET['page'] ?? '', '/');
        $method = strtoupper($_SERVER['REQUEST_METHOD']);

        $handler = $this->routes[$method][$page]
                ?? $this->routes['GET'][$page]
                ?? null;

        if (!$handler) {
            $this->notFound();
            return;
        }

        [$controllerClass, $action] = $handler;

        if (!class_exists($controllerClass)) {
            $this->notFound();
            return;
        }

        $controller = new $controllerClass($db);

        if (!method_exists($controller, $action)) {
            $this->notFound();
            return;
        }

        $controller->$action();
    }

    private function notFound(): void
    {
        http_response_code(404);
        $tpl = APP_PATH . '/views/errors/404.php';
        if (file_exists($tpl)) {
            require $tpl;
        } else {
            echo '<h1>404 — Page introuvable</h1>';
        }
    }
}
