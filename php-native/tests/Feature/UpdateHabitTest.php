<?php

namespace Tests\Feature;

use Tests\TestCase;

class UpdateHabitTest extends TestCase
{
    public function test_update_habit_with_valid_payload(): void
    {
        $createResponse = $this->post('/habits', [
            'name' => 'Morning Run',
            'frequency' => 'daily',
            'target_count' => 30,
        ]);

        $id = $createResponse['body']['id'];

        $response = $this->put('/habits/' . $id, [
            'name' => 'Evening Run',
            'target_count' => 20,
        ]);

        $this->assertEquals(200, $response['status']);
        $this->assertEquals('Evening Run', $response['body']['name']);
        $this->assertEquals(20, $response['body']['target_count']);
        $this->assertEquals('daily', $response['body']['frequency']);
    }

    public function test_update_habit_with_invalid_frequency_returns_422(): void
    {
        $createResponse = $this->post('/habits', [
            'name' => 'Morning Run',
            'frequency' => 'daily',
            'target_count' => 30,
        ]);

        $id = $createResponse['body']['id'];

        $response = $this->put('/habits/' . $id, [
            'frequency' => 'monthly',
        ]);

        $this->assertEquals(422, $response['status']);
        $this->assertArrayHasKey('error', $response['body']);
    }

    public function test_update_habit_not_found_returns_404(): void
    {
        $response = $this->put('/habits/999', [
            'name' => 'Updated',
        ]);

        $this->assertEquals(404, $response['status']);
        $this->assertEquals('Habit not found', $response['body']['error']);
    }
}
