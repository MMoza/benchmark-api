<?php

namespace Tests\Feature;

use Tests\TestCase;

class CompleteHabitTest extends TestCase
{
    public function test_complete_habit_increments_count(): void
    {
        $createResponse = $this->post('/habits', [
            'name' => 'Morning Run',
            'frequency' => 'daily',
            'target_count' => 30,
        ]);

        $id = $createResponse['body']['id'];

        $response = $this->post('/habits/' . $id . '/complete');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals(1, $response['body']['completed_count']);
    }

    public function test_complete_habit_not_found_returns_404(): void
    {
        $response = $this->post('/habits/999/complete');

        $this->assertEquals(404, $response['status']);
        $this->assertEquals('Habit not found', $response['body']['error']);
    }
}
