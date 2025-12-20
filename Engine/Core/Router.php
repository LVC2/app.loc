<?php
declare(strict_types=1);

namespace Engine\Core;

use Engine\Start;
use Error;

/**
 * Класс маршрутизатора. Отвечает за сопоставление URL с контроллером/методом.
 */
class Router
{
    protected Start $app;
    protected array $routes = [];
    protected const API_PREFIX = 'api/';

    protected array $params = [];

    public function __construct(Start $app)
    {
        $this->app = $app;
        $this->loadRoutes();
    }

    protected function loadRoutes(): void
    {
        $routesFile = $this->app->getRootPath() . '/Data/Router.php';
        if (is_file($routesFile)) {
            $router = $this;
            require $routesFile;
        }
    }

    public function add(string $pattern, string $controllerName, string $methodName, string $httpMethod = 'GET', array $options = []): void
    {
        $this->routes[] = array_merge([
            'pattern' => trim($pattern, '/'),
            'controller' => $controllerName,
            'method' => $methodName,
            'http_method' => strtoupper($httpMethod)
        ], $options);
    }

    protected function match(): ?array
    {
        $uri = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '', '/');
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        foreach ($this->routes as $route) {
            if ($route['http_method'] !== $requestMethod) {
                continue;
            }

            $safePattern = preg_quote($route['pattern'], '#');
            $safePattern = str_replace(['\(', '\)'], ['(', ')'], $safePattern);
            $pattern = '#^' . $safePattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                $this->params = array_slice($matches, 1);
                return $route;
            }
        }
        return null;
    }

    /**
     * Запускает приложение: находит маршрут и вызывает контроллер или рендерит оболочку.
     * @throws Error В случае отсутствия контроллера или метода.
     */
    public function dispatch(): void
    {
        $matchedRoute = $this->match();
        $uri = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '', '/');
        $isApiRequest = str_starts_with($uri, self::API_PREFIX);

        if ($matchedRoute) {

            $namespace = $matchedRoute['namespace'] ?? 'App\\Controller';

            $controllerClass = $namespace . '\\' . $matchedRoute['controller'];
            $method = $matchedRoute['method'] . 'Action';

            if (!class_exists($controllerClass)) {
                throw new Error("Controller class {$controllerClass} not found for route '{$matchedRoute['pattern']}'.");
            }

            $controller = new $controllerClass($this->app);

            if (!method_exists($controller, $method)) {
                throw new Error("Controller method {$method} not found in {$controllerClass}.");
            }

            $controller->$method(...$this->params);

        } else {

            if ($isApiRequest) {
                // 404 для API
                header("HTTP/1.0 404 Not Found");
                header('Content-Type: application/json');
                echo json_encode(['error' => 'API Endpoint Not Found']);
            } else {
                $shellControllerClass = 'App\\Controller\\Index';
                $shellMethod = 'indexAction';

                if (!class_exists($shellControllerClass)) {
                    throw new Error("Core shell controller ({$shellControllerClass}) is missing. Cannot render SPA shell.");
                }

                $shellController = new $shellControllerClass($this->app);

                if (!method_exists($shellController, $shellMethod)) {
                    throw new Error("Core shell method {$shellMethod} not found in {$shellControllerClass}.");
                }

                $shellController->$shellMethod();
            }
        }
    }
}