<?php

namespace App\Application\Service;

use App\Domain\Entity\Habit;
use App\Domain\Repository\HabitRepositoryInterface;

class ListHabits
{
    public function __construct(
        private HabitRepositoryInterface $repository
    ) {
    }

    /**
     * @return Habit[]
     */
    public function execute(): array
    {
        return $this->repository->findAll();
    }
}
