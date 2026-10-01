<?php

declare(strict_types=1);

namespace App\Job\Domain;

use App\Shared\Domain\Assert;
use App\Shared\Domain\ValidationException;

/** Entidad de dominio: experiencia laboral. Sin dependencias de framework. */
final class Job
{
    private string $title;
    private string $description;
    private int $months;

    public function __construct(
        private ?int $id,
        string $title,
        string $description,
        int $months = 0,
    ) {
        $this->change($title, $description, $months);
    }

    public static function create(string $title, string $description, int $months): self
    {
        return new self(null, $title, $description, $months);
    }

    public function change(string $title, string $description, int $months): void
    {
        if ($months < 0) {
            throw ValidationException::single('months', 'La duración no puede ser negativa.');
        }
        $this->title = Assert::notBlank($title, 'title');
        $this->description = Assert::notBlank($description, 'description', 2000);
        $this->months = $months;
    }

    public function withId(int $id): self
    {
        $copy = clone $this;
        $copy->id = $id;
        return $copy;
    }

    public function id(): ?int { return $this->id; }
    public function title(): string { return $this->title; }
    public function description(): string { return $this->description; }
    public function months(): int { return $this->months; }

    public function durationAsString(): string
    {
        $years = intdiv($this->months, 12);
        $extra = $this->months % 12;

        return match (true) {
            $this->months === 0 => 'Sin duración registrada',
            $years === 0 => sprintf('%d %s', $extra, $extra === 1 ? 'mes' : 'meses'),
            $extra === 0 => sprintf('%d %s', $years, $years === 1 ? 'año' : 'años'),
            default => sprintf('%d %s y %d %s', $years, $years === 1 ? 'año' : 'años', $extra, $extra === 1 ? 'mes' : 'meses'),
        };
    }
}
