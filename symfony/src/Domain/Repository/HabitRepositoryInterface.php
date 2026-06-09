<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Habit;

interface HabitRepositoryInterface
{
    /**
     * @return Habit[]
     */
    public function findAll(): array;

    public function findById(int $id): ?Habit;

    public function save(Habit $habit): void;
}
