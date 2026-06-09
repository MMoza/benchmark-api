# AGENTS.md

## Project Overview

This repository contains an architectural experiment that implements the same REST API using different PHP ecosystems and architectural approaches.

The purpose of this project is **not to determine which framework is better**, but to compare:

- Development experience
- Project structure
- Architectural complexity
- Maintainability
- Ease of evolution

Each implementation must expose the same API contract and provide the same functional behavior.

---

## Repository Structure

/
├── specification/
├── php-native/
├── laravel/
└── symfony/

### specification

Contains the shared API specification, business rules and acceptance criteria.

This directory is considered the source of truth.

Any change in functionality must be defined here before being implemented in the frameworks.

### php-native

Implementation using:

- PHP 8+
- MVC architecture
- Manual routing
- Manual dependency management
- No framework

### laravel

Implementation using:

- Laravel
- MVC architecture
- Eloquent ORM
- Laravel conventions

### symfony

Implementation using:

- Symfony
- DDD-inspired architecture
- Dependency Injection
- Doctrine ORM
- Application / Domain / Infrastructure separation

## Domain

Habit Tracking API

### Habit Entity

- id
- name
- description
- frequency
- target_count
- completed_count
- created_at

Allowed frequencies:

- daily
- weekly

## Functional Requirements

- POST /habits
- GET /habits
- GET /habits/{id}
- PUT /habits/{id}
- POST /habits/{id}/complete

## API Consistency Rules

All implementations must:

- Use the same request payloads
- Use the same response payloads
- Return equivalent HTTP status codes
- Enforce the same business rules

## Comparison Goals

### Quantitative

- Files created
- Lines of code
- Dependencies
- Number of classes
- Test coverage

### Qualitative

- Ease of implementation
- Readability
- Boilerplate required
- Framework ergonomics
- Ease of adding new features

## Non Goals

This project does not attempt to compare:

- Raw performance
- Request throughput
- Memory consumption
- Framework benchmarks

The primary focus is architecture and developer experience.

## Guiding Principle

Prioritize:

1. Clarity
2. Idiomatic framework usage
3. Architectural consistency
4. Comparability between implementations
