<?php

namespace App\Models;

use App\Database\Connection;
use PDO;

class Habit
{
    public ?int $id = null;
    public string $name;
    public ?string $description = null;
    public string $frequency;
    public int $target_count;
    public int $completed_count = 0;
    public string $created_at;

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'frequency' => $this->frequency,
            'target_count' => $this->target_count,
            'completed_count' => $this->completed_count,
            'created_at' => $this->created_at,
        ];
    }

    public static function fromArray(array $data): self
    {
        $habit = new self();
        $habit->id = $data['id'] ?? null;
        $habit->name = $data['name'];
        $habit->description = $data['description'] ?? null;
        $habit->frequency = $data['frequency'];
        $habit->target_count = (int) $data['target_count'];
        $habit->completed_count = (int) ($data['completed_count'] ?? 0);
        $habit->created_at = $data['created_at'] ?? date('Y-m-d H:i:s');
        return $habit;
    }

    public static function all(): array
    {
        $pdo = Connection::getInstance();
        $stmt = $pdo->query('SELECT * FROM habits ORDER BY created_at DESC');
        $rows = $stmt->fetchAll();

        return array_map(fn($row) => self::fromArray($row), $rows);
    }

    public static function find(int $id): ?self
    {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM habits WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row ? self::fromArray($row) : null;
    }

    public function save(): self
    {
        $pdo = Connection::getInstance();

        if ($this->id === null) {
            $stmt = $pdo->prepare('
                INSERT INTO habits (name, description, frequency, target_count, completed_count, created_at)
                VALUES (?, ?, ?, ?, ?, ?)
            ');
            $stmt->execute([
                $this->name,
                $this->description,
                $this->frequency,
                $this->target_count,
                $this->completed_count,
                $this->created_at,
            ]);
            $this->id = (int) $pdo->lastInsertId();
        } else {
            $stmt = $pdo->prepare('
                UPDATE habits
                SET name = ?, description = ?, frequency = ?, target_count = ?, completed_count = ?
                WHERE id = ?
            ');
            $stmt->execute([
                $this->name,
                $this->description,
                $this->frequency,
                $this->target_count,
                $this->completed_count,
                $this->id,
            ]);
        }

        return $this;
    }

    public function complete(): self
    {
        if ($this->completed_count < $this->target_count) {
            $this->completed_count++;
            $this->save();
        }

        return $this;
    }
}
