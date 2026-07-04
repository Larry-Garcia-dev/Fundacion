<?php
class Router
{
    private string $defaultRoute;
    private string $namespace;
    private string $viewsDir;

    public function __construct(string $defaultRoute, string $namespace, string $viewsDir)
    {
        $this->defaultRoute = $defaultRoute;
        $this->namespace    = $namespace;
        $this->viewsDir     = $viewsDir;
    }

    public function dispatch(): void
    {
        $route  = $_GET['route'] ?? $this->defaultRoute;
        $route  = preg_replace('/[^a-zA-Z0-9_]/', '', $route);
        $action = $_GET['action'] ?? 'index';
        $action = preg_replace('/[^a-zA-Z0-9_]/', '', $action);

        $controllerName = ucfirst($route) . 'Controller';
        $controllerFile = $this->namespace . '/' . $controllerName . '.php';

        if (!file_exists($controllerFile)) {
            http_response_code(404);
            echo 'Página no encontrada.';
            return;
        }

        require_once $controllerFile;

        $controller = new $controllerName();
        $method = $action . 'Action';

        if (!method_exists($controller, $method)) {
            $method = 'indexAction';
        }

        $controller->$method();
    }
}
