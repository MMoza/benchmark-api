<?php

namespace App\Infrastructure\Doctrine;

use App\Domain\Entity\Habit;
use App\Domain\Repository\HabitRepositoryInterface;
use App\Domain\ValueObject\Frequency;
use Doctrine\DBAL\Connection;

class DoctrineHabitRepository implements HabitRepositoryInterface
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function findAll(): array
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT * FROM habit ORDER BY created_at DESC'
        );

        return array_map(fn($row) => $this->mapToEntity($row), $rows);
    }

    public function findById(int $id): ?Habit
    {
        $row = $this->connection->fetchAssociative(
            'SELECT * FROM habit WHERE id = ?',
            [$id]
        );

        return $row ? $this->mapToEntity($row) : null;
    }

    public function save(Habit $habit): void
    {
        if ($habit->getId() === null) {
            $this->connection->insert('habit', [
                'name' => $habit->getName(),
                'description' => $habit->getDescription(),
                'frequency' => $habit->getFrequency()->value,
                'target_count' => $habit->getTargetCount(),
                'completed_count' => $habit->getCompletedCount(),
                'created_at' => $habit->getCreatedAt()->format('Y-m-d H:i:s'),
            ]);

            $habit->setId((int) $this->connection->lastInsertId());
        } else {
            $this->connection->update('habit', [
                'name' => $habit->getName(),
                'description' => $habit->getDescription(),
                'frequency' => $habit->getFrequency()->value,
                'target_count' => $habit->getTargetCount(),
                'completed_count' => $habit->getCompletedCount(),
            ], ['id' => $habit->getId()]);
        }
    }

    private function mapToEntity(array $row): Habit
    {
        $habit = new Habit(
            name: $row['name'],
            frequency: Frequency::from($row['frequency']),
            targetCount: (int) $row['target_count'],
            description: $row['description'] ?? null
        );

        $habit->setId((int) $row['id']);
        $habit->setCreatedAt(new \DateTimeImmutable($row['created_at']));

        $reflection = new \ReflectionProperty(Habit::class, 'completedCount');
        $reflection->setValue($habit, (int) $row['completed_count']);

        return $habit;
    }
}
