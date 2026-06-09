<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Habit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HabitController extends Controller
{
    public function index(): JsonResponse
    {
        $habits = Habit::orderBy('created_at', 'desc')->get();

        return response()->json($habits);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'         => 'required|string',
            'description'  => 'nullable|string',
            'frequency'    => 'required|in:daily,weekly',
            'target_count' => 'required|integer',
        ]);

        $habit = Habit::create($validated);

        return response()->json($habit, 201);
    }

    public function show(int $id): JsonResponse
    {
        $habit = Habit::find($id);

        if (!$habit) {
            return response()->json(['error' => 'Habit not found'], 404);
        }

        return response()->json($habit);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $habit = Habit::find($id);

        if (!$habit) {
            return response()->json(['error' => 'Habit not found'], 404);
        }

        $validated = $request->validate([
            'name'         => 'sometimes|string',
            'description'  => 'nullable|string',
            'frequency'    => 'sometimes|in:daily,weekly',
            'target_count' => 'sometimes|integer',
        ]);

        $habit->update($validated);

        return response()->json($habit);
    }

    public function complete(int $id): JsonResponse
    {
        $habit = Habit::find($id);

        if (!$habit) {
            return response()->json(['error' => 'Habit not found'], 404);
        }

        if ($habit->completed_count < $habit->target_count) {
            $habit->increment('completed_count');
        }

        return response()->json($habit->fresh());
    }
}
