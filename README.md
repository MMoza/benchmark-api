# Benchmark API - Architectural Experiment

This repository contains an architectural experiment that implements the same REST API using different PHP ecosystems and architectural approaches.

## Purpose

The purpose of this project is **not to determine which framework is better**, but to compare:

- Development experience
- Project structure
- Architectural complexity
- Maintainability
- Ease of evolution

Each implementation exposes the same API contract and provides the same functional behavior.

## Domain

**Habit Tracking API** - A simple API to track habits with the following endpoints:

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/habits` | Create a new habit |
| GET | `/habits` | List all habits |
| GET | `/habits/{id}` | Get a specific habit |
| PUT | `/habits/{id}` | Update a habit |
| POST | `/habits/{id}/complete` | Register habit completion |

## Project Structure

```
/
├── specification/      # OpenAPI specification (source of truth)
├── php-native/         # PHP 8+ implementation (no framework)
├── laravel/            # Laravel implementation
└── symfony/            # Symfony implementation
```

### Implementations

| Implementation | Architecture | ORM | Routing |
|---------------|--------------|-----|---------|
| **php-native** | MVC | Manual | Manual |
| **laravel** | MVC | Eloquent | Laravel Router |
| **symfony** | DDD-inspired | Doctrine | Symfony Router |

## Prerequisites

- PHP 8.3+ (8.4+ for Symfony)
- Composer
- SQLite (default database for all implementations)

## Setup & Run

### PHP Native

```bash
cd php-native
composer install
php -S localhost:8001 -t public/
```

### Laravel

```bash
cd laravel
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve --port=8002
```

### Symfony

```bash
cd symfony
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
symfony server:start --port=8003
# Or use PHP built-in server:
php -S localhost:8003 -t public/
```

## Running Tests

### PHP Native

```bash
cd php-native
./vendor/bin/phpunit
```

### Laravel

```bash
cd laravel
composer test
# Or directly:
php artisan test
```

### Symfony

```bash
cd symfony
./vendor/bin/phpunit
```

## API Specification

The OpenAPI specification is located at `specification/openapi.yaml` and serves as the source of truth for all implementations.

### Example Requests

**Create a habit:**
```bash
curl -X POST http://localhost:8001/habits \
  -H "Content-Type: application/json" \
  -d '{"name": "Exercise", "frequency": "daily", "target_count": 5}'
```

**List habits:**
```bash
curl http://localhost:8001/habits
```

**Complete a habit:**
```bash
curl -X POST http://localhost:8001/habits/1/complete
```

## Comparison Goals

### Quantitative Metrics
- Files created
- Lines of code
- Dependencies
- Number of classes
- Test coverage

### Qualitative Metrics
- Ease of implementation
- Readability
- Boilerplate required
- Framework ergonomics
- Ease of adding new features

## Non Goals

This project does **not** compare:
- Raw performance
- Request throughput
- Memory consumption
- Framework benchmarks

## Guiding Principles

1. Clarity
2. Idiomatic framework usage
3. Architectural consistency
4. Comparability between implementations
