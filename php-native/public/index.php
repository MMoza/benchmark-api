<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Router\Router;
use App\Controllers\HabitController;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$router     = new Router();
$controller = new HabitController();

$router->addRoute('POST', '/habits', fn() => $controller->store());
$router->addRoute('GET', '/habits', fn() => $controller->index());
$router->addRoute('GET', '/habits/{id}', fn($params) => $controller->show($params));
$router->addRoute('PUT', '/habits/{id}', fn($params) => $controller->update($params));
$router->addRoute('POST', '/habits/{id}/complete', fn($params) => $controller->complete($params));

$router->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
