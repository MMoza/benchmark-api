<?php

namespace App\Controllers;

use App\Models\Habit;

class HabitController
{
    public function index(): void
    {
        $habits = Habit::all();
        $this->json(array_map(fn($h) => $h->toArray(), $habits));
    }

    public function store(): void
    {
        $data = $this->getJsonBody();
        $errors = $this->validateCreate($data);

        if ($errors) {
            $this->json(['error' => $errors], 422);
            return;
        }

        $habit = Habit::fromArray($data);
        $habit->save();

        $this->json($habit->toArray(), 201);
    }

    public function show(array $params): void
    {
        $habit = Habit::find((int) $params['id']);

        if (!$habit) {
            $this->json(['error' => 'Habit not found'], 404);
            return;
        }

        $this->json($habit->toArray());
    }

    public function update(array $params): void
    {
        $habit = Habit::find((int) $params['id']);

        if (!$habit) {
            $this->json(['error' => 'Habit not found'], 404);
            return;
        }

        $data = $this->getJsonBody();
        $errors = $this->validateUpdate($data);

        if ($errors) {
            $this->json(['error' => $errors], 422);
            return;
        }

        foreach ($data as $key => $value) {
            if ($value !== null) {
                $habit->$key = $value;
            }
        }

        $habit->save();
        $this->json($habit->toArray());
    }

    public function complete(array $params): void
    {
        $habit = Habit::find((int) $params['id']);

        if (!$habit) {
            $this->json(['error' => 'Habit not found'], 404);
            return;
        }

        $habit->complete();
        $this->json($habit->toArray());
    }

    private function validateCreate(array $data): ?string
    {
        if (empty($data['name'])) {
            return 'The name field is required.';
        }
        if (empty($data['frequency'])) {
            return 'The frequency field is required.';
        }
        if (!in_array($data['frequency'], ['daily', 'weekly'])) {
            return 'The frequency must be daily or weekly.';
        }
        if (!isset($data['target_count']) || !is_numeric($data['target_count'])) {
            return 'The target_count field is required.';
        }

        return null;
    }

    private function validateUpdate(array $data): ?string
    {
        if (isset($data['frequency']) && !in_array($data['frequency'], ['daily', 'weekly'])) {
            return 'The frequency must be daily or weekly.';
        }

        return null;
    }

    private function getJsonBody(): array
    {
        if (isset($GLOBALS['__test_input'])) {
            return json_decode($GLOBALS['__test_input'], true) ?? [];
        }
        $body = file_get_contents('php://input');
        return json_decode($body, true) ?? [];
    }

    private function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
