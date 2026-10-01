<?php

declare(strict_types=1);

namespace App\Project\Domain;

use App\Shared\Domain\Assert;

/** Entidad de dominio: proyecto del portafolio. */
final class Project
{
    private string $title;
    private string $description;
    /** @var list<string> */
    private array $technologies;

    /** @param list<string> $technologies */
    public function __construct(
        private ?int $id,
        string $title,
        string $description,
        array $technologies = [],
    ) {
        $this->change($title, $description, $technologies);
    }

    /** @param list<string> $technologies */
    public static function create(string $title, string $description, array $technologies = []): self
    {
        return new self(null, $title, $description, $technologies);
    }

    /** @param list<string> $technologies */
    public function change(string $title, string $description, array $technologies = []): void
    {
        $this->title = Assert::notBlank($title, 'title');
        $this->description = Assert::notBlank($description, 'description', 2000);
        $this->technologies = array_values(array_unique(array_filter(array_map('trim', $technologies))));
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
    /** @return list<string> */
    public function technologies(): array { return $this->technologies; }
}
