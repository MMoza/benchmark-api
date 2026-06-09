<?php

namespace App\Application\Service;

use App\Domain\Entity\Habit;
use App\Domain\Repository\HabitRepositoryInterface;
use App\Domain\ValueObject\Frequency;

class UpdateHabit
{
    public function __construct(
        private HabitRepositoryInterface $repository,
        private GetHabit $getHabit
    ) {
    }

    public function execute(
        int $id,
        ?string $name = null,
        ?string $frequency = null,
        ?int $targetCount = null,
        ?string $description = null
    ): ?Habit {
        $habit = $this->getHabit->execute($id);

        if ($habit === null) {
            return null;
        }

        if ($name !== null) {
            $habit->setName($name);
        }
        if ($frequency !== null) {
            $habit->setFrequency(Frequency::from($frequency));
        }
        if ($targetCount !== null) {
            $habit->setTargetCount($targetCount);
        }
        if ($description !== null) {
            $habit->setDescription($description);
        }

        $this->repository->save($habit);

        return $habit;
    }
}
