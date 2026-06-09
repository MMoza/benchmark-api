<?php

namespace App\Domain\Entity;

use App\Domain\ValueObject\Frequency;

class Habit
{
    private ?int $id = null;
    private string $name;
    private ?string $description = null;
    private Frequency $frequency;
    private int $targetCount;
    private int $completedCount = 0;
    private \DateTimeImmutable $createdAt;

    public function __construct(
        string $name,
        Frequency $frequency,
        int $targetCount,
        ?string $description = null
    ) {
        $this->name = $name;
        $this->frequency = $frequency;
        $this->targetCount = $targetCount;
        $this->description = $description;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getFrequency(): Frequency
    {
        return $this->frequency;
    }

    public function setFrequency(Frequency $frequency): void
    {
        $this->frequency = $frequency;
    }

    public function getTargetCount(): int
    {
        return $this->targetCount;
    }

    public function setTargetCount(int $targetCount): void
    {
        $this->targetCount = $targetCount;
    }

    public function getCompletedCount(): int
    {
        return $this->completedCount;
    }

    public function complete(): void
    {
        if ($this->completedCount < $this->targetCount) {
            $this->completedCount++;
        }
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'frequency' => $this->frequency->value,
            'target_count' => $this->targetCount,
            'completed_count' => $this->completedCount,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
        ];
    }
}
