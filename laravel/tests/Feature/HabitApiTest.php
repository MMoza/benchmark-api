<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HabitApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_habit_with_valid_payload(): void
    {
        $response = $this->postJson('/api/habits', [
            'name' => 'Morning Run',
            'description' => 'Run 5km every morning',
            'frequency' => 'daily',
            'target_count' => 30,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'name', 'frequency', 'target_count', 'created_at'])
            ->assertJson([
                'name' => 'Morning Run',
                'frequency' => 'daily',
                'target_count' => 30,
            ]);
    }

    public function test_create_habit_without_name_returns_422(): void
    {
        $response = $this->postJson('/api/habits', [
            'frequency' => 'daily',
            'target_count' => 10,
        ]);

        $response->assertStatus(422);
    }

    public function test_create_habit_without_frequency_returns_422(): void
    {
        $response = $this->postJson('/api/habits', [
            'name' => 'Test',
            'target_count' => 10,
        ]);

        $response->assertStatus(422);
    }

    public function test_create_habit_with_invalid_frequency_returns_422(): void
    {
        $response = $this->postJson('/api/habits', [
            'name' => 'Test',
            'frequency' => 'monthly',
            'target_count' => 10,
        ]);

        $response->assertStatus(422);
    }

    public function test_create_habit_without_target_count_returns_422(): void
    {
        $response = $this->postJson('/api/habits', [
            'name' => 'Test',
            'frequency' => 'daily',
        ]);

        $response->assertStatus(422);
    }

    public function test_list_habits_returns_empty_array(): void
    {
        $response = $this->getJson('/api/habits');

        $response->assertStatus(200)
            ->assertJson([]);
    }

    public function test_list_habits_returns_created_habits(): void
    {
        $this->postJson('/api/habits', [
            'name' => 'Habit 1',
            'frequency' => 'daily',
            'target_count' => 10,
        ]);

        $this->postJson('/api/habits', [
            'name' => 'Habit 2',
            'frequency' => 'weekly',
            'target_count' => 5,
        ]);

        $response = $this->getJson('/api/habits');

        $response->assertStatus(200)
            ->assertJsonCount(2);
    }

    public function test_show_habit_returns_habit(): void
    {
        $createResponse = $this->postJson('/api/habits', [
            'name' => 'Morning Run',
            'frequency' => 'daily',
            'target_count' => 30,
        ]);

        $id = $createResponse->json('id');

        $response = $this->getJson('/api/habits/' . $id);

        $response->assertStatus(200)
            ->assertJson(['id' => $id, 'name' => 'Morning Run']);
    }

    public function test_show_habit_not_found_returns_404(): void
    {
        $response = $this->getJson('/api/habits/999');

        $response->assertStatus(404)
            ->assertJson(['error' => 'Habit not found']);
    }

    public function test_update_habit_with_valid_payload(): void
    {
        $createResponse = $this->postJson('/api/habits', [
            'name' => 'Morning Run',
            'frequency' => 'daily',
            'target_count' => 30,
        ]);

        $id = $createResponse->json('id');

        $response = $this->putJson('/api/habits/' . $id, [
            'name' => 'Evening Run',
            'target_count' => 20,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'Evening Run',
                'target_count' => 20,
                'frequency' => 'daily',
            ]);
    }

    public function test_update_habit_with_invalid_frequency_returns_422(): void
    {
        $createResponse = $this->postJson('/api/habits', [
            'name' => 'Morning Run',
            'frequency' => 'daily',
            'target_count' => 30,
        ]);

        $id = $createResponse->json('id');

        $response = $this->putJson('/api/habits/' . $id, [
            'frequency' => 'monthly',
        ]);

        $response->assertStatus(422);
    }

    public function test_update_habit_not_found_returns_404(): void
    {
        $response = $this->putJson('/api/habits/999', [
            'name' => 'Updated',
        ]);

        $response->assertStatus(404)
            ->assertJson(['error' => 'Habit not found']);
    }

    public function test_complete_habit_increments_count(): void
    {
        $createResponse = $this->postJson('/api/habits', [
            'name' => 'Morning Run',
            'frequency' => 'daily',
            'target_count' => 30,
        ]);

        $id = $createResponse->json('id');

        $response = $this->postJson('/api/habits/' . $id . '/complete');

        $response->assertStatus(200)
            ->assertJson(['completed_count' => 1]);
    }

    public function test_complete_habit_not_found_returns_404(): void
    {
        $response = $this->postJson('/api/habits/999/complete');

        $response->assertStatus(404)
            ->assertJson(['error' => 'Habit not found']);
    }
}
