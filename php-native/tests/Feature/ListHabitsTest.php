<?php

namespace Tests\Feature;

use Tests\TestCase;

class ListHabitsTest extends TestCase
{
    public function test_list_habits_returns_empty_array(): void
    {
        $response = $this->get('/habits');

        $this->assertEquals(200, $response['status']);
        $this->assertIsArray($response['body']);
        $this->assertEmpty($response['body']);
    }

    public function test_list_habits_returns_created_habits(): void
    {
        $this->post('/habits', [
            'name' => 'Habit 1',
            'frequency' => 'daily',
            'target_count' => 10,
        ]);

        $this->post('/habits', [
            'name' => 'Habit 2',
            'frequency' => 'weekly',
            'target_count' => 5,
        ]);

        $response = $this->get('/habits');

        $this->assertEquals(200, $response['status']);
        $this->assertCount(2, $response['body']);
        $names = array_column($response['body'], 'name');
        $this->assertContains('Habit 1', $names);
        $this->assertContains('Habit 2', $names);
    }
}
