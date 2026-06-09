<?php

namespace App\Application\Service;

use App\Domain\Entity\Habit;
use App\Domain\Repository\HabitRepositoryInterface;
use App\Domain\ValueObject\Frequency;

class CreateHabit
{
    public function __construct(
        private HabitRepositoryInterface $repository
    ) {
    }

    public function execute(
        string $name,
        string $frequency,
        int $targetCount,
        ?string $description = null
    ): Habit {
        $habit = new Habit(
            name: $name,
            frequency: Frequency::from($frequency),
            targetCount: $targetCount,
            description: $description
        );

        $this->repository->save($habit);

        return $habit;
    }
}
