<?php

namespace App\Infrastructure\Http\Controller;

use App\Application\Service\CompleteHabit;
use App\Application\Service\CreateHabit;
use App\Application\Service\GetHabit;
use App\Application\Service\ListHabits;
use App\Application\Service\UpdateHabit;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HabitController
{
    #[Route('/habits', methods: ['POST'])]
    public function store(Request $request, CreateHabit $createHabit): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['name'])) {
            return new JsonResponse(['error' => 'The name field is required.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        if (empty($data['frequency'])) {
            return new JsonResponse(['error' => 'The frequency field is required.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        if (!in_array($data['frequency'], ['daily', 'weekly'])) {
            return new JsonResponse(['error' => 'The frequency must be daily or weekly.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        if (!isset($data['target_count']) || !is_numeric($data['target_count'])) {
            return new JsonResponse(['error' => 'The target_count field is required.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $habit = $createHabit->execute(
            name: $data['name'],
            frequency: $data['frequency'],
            targetCount: (int) $data['target_count'],
            description: $data['description'] ?? null
        );

        return new JsonResponse($habit->toArray(), Response::HTTP_CREATED);
    }

    #[Route('/habits', methods: ['GET'])]
    public function index(ListHabits $listHabits): JsonResponse
    {
        $habits = $listHabits->execute();

        return new JsonResponse(array_map(fn($h) => $h->toArray(), $habits));
    }

    #[Route('/habits/{id}', methods: ['GET'])]
    public function show(int $id, GetHabit $getHabit): JsonResponse
    {
        $habit = $getHabit->execute($id);

        if ($habit === null) {
            return new JsonResponse(['error' => 'Habit not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($habit->toArray());
    }

    #[Route('/habits/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request, UpdateHabit $updateHabit): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['frequency']) && !in_array($data['frequency'], ['daily', 'weekly'])) {
            return new JsonResponse(['error' => 'The frequency must be daily or weekly.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $habit = $updateHabit->execute(
            id: $id,
            name: $data['name'] ?? null,
            frequency: $data['frequency'] ?? null,
            targetCount: isset($data['target_count']) ? (int) $data['target_count'] : null,
            description: $data['description'] ?? null
        );

        if ($habit === null) {
            return new JsonResponse(['error' => 'Habit not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($habit->toArray());
    }

    #[Route('/habits/{id}/complete', methods: ['POST'])]
    public function complete(int $id, CompleteHabit $completeHabit): JsonResponse
    {
        $habit = $completeHabit->execute($id);

        if ($habit === null) {
            return new JsonResponse(['error' => 'Habit not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($habit->toArray());
    }
}
