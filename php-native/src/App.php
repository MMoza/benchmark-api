<?php

namespace App;

use App\Router\Router;
use App\Controllers\HabitController;

class App
{
    private Router $router;
    private HabitController $controller;

    public function __construct()
    {
        $this->router = new Router();
        $this->controller = new HabitController();
        $this->registerRoutes();
    }

    private function registerRoutes(): void
    {
        $this->router->addRoute('POST', '/habits', fn() => $this->controller->store());
        $this->router->addRoute('GET', '/habits', fn() => $this->controller->index());
        $this->router->addRoute('GET', '/habits/{id}', fn($params) => $this->controller->show($params));
        $this->router->addRoute('PUT', '/habits/{id}', fn($params) => $this->controller->update($params));
        $this->router->addRoute('POST', '/habits/{id}/complete', fn($params) => $this->controller->complete($params));
    }

    public function handle(string $method, string $uri, ?array $body = null): array
    {
        ob_start();

        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI'] = $uri;

        if ($body !== null) {
            $GLOBALS['__test_input'] = json_encode($body);
        }

        $this->router->dispatch($method, $uri);

        $output = ob_get_clean();
        $statusCode = http_response_code();
        http_response_code(200);

        unset($GLOBALS['__test_input']);

        return [
            'status' => $statusCode,
            'body' => json_decode($output, true),
        ];
    }
}
