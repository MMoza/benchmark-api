<?php

namespace Tests\Feature;

use Tests\TestCase;

class CreateHabitTest extends TestCase
{
    public function test_create_habit_with_valid_payload(): void
    {
        $response = $this->post('/habits', [
            'name' => 'Morning Run',
            'description' => 'Run 5km every morning',
            'frequency' => 'daily',
            'target_count' => 30,
        ]);

        $this->assertEquals(201, $response['status']);
        $this->assertIsInt($response['body']['id']);
        $this->assertEquals('Morning Run', $response['body']['name']);
        $this->assertEquals('daily', $response['body']['frequency']);
        $this->assertEquals(30, $response['body']['target_count']);
        $this->assertEquals(0, $response['body']['completed_count']);
        $this->assertArrayHasKey('created_at', $response['body']);
    }

    public function test_create_habit_without_name_returns_422(): void
    {
        $response = $this->post('/habits', [
            'frequency' => 'daily',
            'target_count' => 10,
        ]);

        $this->assertEquals(422, $response['status']);
        $this->assertArrayHasKey('error', $response['body']);
    }

    public function test_create_habit_without_frequency_returns_422(): void
    {
        $response = $this->post('/habits', [
            'name' => 'Test',
            'target_count' => 10,
        ]);

        $this->assertEquals(422, $response['status']);
        $this->assertArrayHasKey('error', $response['body']);
    }

    public function test_create_habit_with_invalid_frequency_returns_422(): void
    {
        $response = $this->post('/habits', [
            'name' => 'Test',
            'frequency' => 'monthly',
            'target_count' => 10,
        ]);

        $this->assertEquals(422, $response['status']);
        $this->assertArrayHasKey('error', $response['body']);
    }

    public function test_create_habit_without_target_count_returns_422(): void
    {
        $response = $this->post('/habits', [
            'name' => 'Test',
            'frequency' => 'daily',
        ]);

        $this->assertEquals(422, $response['status']);
        $this->assertArrayHasKey('error', $response['body']);
    }
}
