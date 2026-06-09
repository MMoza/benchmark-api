<?php

namespace App\Application\Service;

use App\Domain\Entity\Habit;
use App\Domain\Repository\HabitRepositoryInterface;

class GetHabit
{
    public function __construct(
        private HabitRepositoryInterface $repository
    ) {
    }

    public function execute(int $id): ?Habit
    {
        return $this->repository->findById($id);
    }
}
