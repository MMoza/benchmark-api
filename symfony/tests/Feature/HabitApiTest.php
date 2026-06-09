<?php

namespace App\Tests\Feature;

use App\Kernel;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HabitApiTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->ensureKernelShutdown();
        $this->client = static::createClient();

        $this->resetDatabase();
    }

    private function resetDatabase(): void
    {
        $container = static::getContainer();
        $connection = $container->get(Connection::class);

        $connection->executeStatement('DELETE FROM habit');

        try {
            $connection->executeStatement("DELETE FROM sqlite_sequence WHERE name='habit'");
        } catch (\Exception $e) {
        }
    }

    public function test_create_habit_with_valid_payload(): void
    {
        $this->client->jsonRequest('POST', '/habits', [
            'name' => 'Morning Run',
            'description' => 'Run 5km every morning',
            'frequency' => 'daily',
            'target_count' => 30,
        ]);

        $this->assertResponseStatusCodeSame(201);
        $response = $this->client->getResponse()->getContent();
        $data = json_decode($response, true);

        $this->assertIsInt($data['id']);
        $this->assertEquals('Morning Run', $data['name']);
        $this->assertEquals('daily', $data['frequency']);
        $this->assertEquals(30, $data['target_count']);
        $this->assertEquals(0, $data['completed_count']);
        $this->assertArrayHasKey('created_at', $data);
    }

    public function test_create_habit_without_name_returns_422(): void
    {
        $this->client->jsonRequest('POST', '/habits', [
            'frequency' => 'daily',
            'target_count' => 10,
        ]);

        $this->assertResponseStatusCodeSame(422);
    }

    public function test_create_habit_without_frequency_returns_422(): void
    {
        $this->client->jsonRequest('POST', '/habits', [
            'name' => 'Test',
            'target_count' => 10,
        ]);

        $this->assertResponseStatusCodeSame(422);
    }

    public function test_create_habit_with_invalid_frequency_returns_422(): void
    {
        $this->client->jsonRequest('POST', '/habits', [
            'name' => 'Test',
            'frequency' => 'monthly',
            'target_count' => 10,
        ]);

        $this->assertResponseStatusCodeSame(422);
    }

    public function test_create_habit_without_target_count_returns_422(): void
    {
        $this->client->jsonRequest('POST', '/habits', [
            'name' => 'Test',
            'frequency' => 'daily',
        ]);

        $this->assertResponseStatusCodeSame(422);
    }

    public function test_list_habits_returns_empty_array(): void
    {
        $this->client->request('GET', '/habits');

        $this->assertResponseStatusCodeSame(200);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($data);
        $this->assertEmpty($data);
    }

    public function test_list_habits_returns_created_habits(): void
    {
        $this->client->jsonRequest('POST', '/habits', [
            'name' => 'Habit 1',
            'frequency' => 'daily',
            'target_count' => 10,
        ]);

        $this->client->jsonRequest('POST', '/habits', [
            'name' => 'Habit 2',
            'frequency' => 'weekly',
            'target_count' => 5,
        ]);

        $this->client->request('GET', '/habits');

        $this->assertResponseStatusCodeSame(200);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertCount(2, $data);
        $names = array_column($data, 'name');
        $this->assertContains('Habit 1', $names);
        $this->assertContains('Habit 2', $names);
    }

    public function test_show_habit_returns_habit(): void
    {
        $this->client->jsonRequest('POST', '/habits', [
            'name' => 'Morning Run',
            'frequency' => 'daily',
            'target_count' => 30,
        ]);

        $id = json_decode($this->client->getResponse()->getContent(), true)['id'];

        $this->client->request('GET', '/habits/' . $id);

        $this->assertResponseStatusCodeSame(200);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals($id, $data['id']);
        $this->assertEquals('Morning Run', $data['name']);
    }

    public function test_show_habit_not_found_returns_404(): void
    {
        $this->client->request('GET', '/habits/999');

        $this->assertResponseStatusCodeSame(404);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Habit not found', $data['error']);
    }

    public function test_update_habit_with_valid_payload(): void
    {
        $this->client->jsonRequest('POST', '/habits', [
            'name' => 'Morning Run',
            'frequency' => 'daily',
            'target_count' => 30,
        ]);

        $id = json_decode($this->client->getResponse()->getContent(), true)['id'];

        $this->client->jsonRequest('PUT', '/habits/' . $id, [
            'name' => 'Evening Run',
            'target_count' => 20,
        ]);

        $this->assertResponseStatusCodeSame(200);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Evening Run', $data['name']);
        $this->assertEquals(20, $data['target_count']);
        $this->assertEquals('daily', $data['frequency']);
    }

    public function test_update_habit_with_invalid_frequency_returns_422(): void
    {
        $this->client->jsonRequest('POST', '/habits', [
            'name' => 'Morning Run',
            'frequency' => 'daily',
            'target_count' => 30,
        ]);

        $id = json_decode($this->client->getResponse()->getContent(), true)['id'];

        $this->client->jsonRequest('PUT', '/habits/' . $id, [
            'frequency' => 'monthly',
        ]);

        $this->assertResponseStatusCodeSame(422);
    }

    public function test_update_habit_not_found_returns_404(): void
    {
        $this->client->jsonRequest('PUT', '/habits/999', [
            'name' => 'Updated',
        ]);

        $this->assertResponseStatusCodeSame(404);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Habit not found', $data['error']);
    }

    public function test_complete_habit_increments_count(): void
    {
        $this->client->jsonRequest('POST', '/habits', [
            'name' => 'Morning Run',
            'frequency' => 'daily',
            'target_count' => 30,
        ]);

        $id = json_decode($this->client->getResponse()->getContent(), true)['id'];

        $this->client->jsonRequest('POST', '/habits/' . $id . '/complete');

        $this->assertResponseStatusCodeSame(200);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(1, $data['completed_count']);
    }

    public function test_complete_habit_not_found_returns_404(): void
    {
        $this->client->jsonRequest('POST', '/habits/999/complete');

        $this->assertResponseStatusCodeSame(404);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Habit not found', $data['error']);
    }
}
