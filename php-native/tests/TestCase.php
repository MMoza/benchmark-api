<?php

namespace Tests;

use App\App;
use App\Database\Connection;
use PDO;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected App $app;

    protected function setUp(): void
    {
        $this->app = new App();

        $pdo = Connection::getInstance();
        $pdo->exec('DELETE FROM habits');
        $pdo->exec("DELETE FROM sqlite_sequence WHERE name='habits'");
    }

    protected function post(string $uri, ?array $body = null): array
    {
        return $this->app->handle('POST', $uri, $body);
    }

    protected function get(string $uri): array
    {
        return $this->app->handle('GET', $uri);
    }

    protected function put(string $uri, array $body): array
    {
        return $this->app->handle('PUT', $uri, $body);
    }
}
