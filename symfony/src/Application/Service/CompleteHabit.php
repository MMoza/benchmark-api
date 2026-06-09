<?php

namespace App\Application\Service;

use App\Domain\Entity\Habit;
use App\Domain\Repository\HabitRepositoryInterface;

class CompleteHabit
{
    public function __construct(
        private HabitRepositoryInterface $repository,
        private GetHabit $getHabit
    ) {
    }

    public function execute(int $id): ?Habit
    {
        $habit = $this->getHabit->execute($id);

        if ($habit === null) {
            return null;
        }

        $habit->complete();
        $this->repository->save($habit);

        return $habit;
    }
}
