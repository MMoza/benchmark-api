<?php

namespace Tests\Feature;

use Tests\TestCase;

class ShowHabitTest extends TestCase
{
    public function test_show_habit_returns_habit(): void
    {
        $createResponse = $this->post('/habits', [
            'name' => 'Morning Run',
            'frequency' => 'daily',
            'target_count' => 30,
        ]);

        $id = $createResponse['body']['id'];

        $response = $this->get('/habits/' . $id);

        $this->assertEquals(200, $response['status']);
        $this->assertEquals($id, $response['body']['id']);
        $this->assertEquals('Morning Run', $response['body']['name']);
    }

    public function test_show_habit_not_found_returns_404(): void
    {
        $response = $this->get('/habits/999');

        $this->assertEquals(404, $response['status']);
        $this->assertEquals('Habit not found', $response['body']['error']);
    }
}
