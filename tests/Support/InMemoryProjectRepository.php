<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Project\Domain\Project;
use App\Project\Domain\ProjectRepository;

final class InMemoryProjectRepository implements ProjectRepository
{
    /** @var array<int, Project> */
    private array $items = [];
    private int $nextId = 1;

    public function all(): array { return array_values($this->items); }
    public function find(int $id): ?Project { return isset($this->items[$id]) ? clone $this->items[$id] : null; }

    public function save(Project $project): Project
    {
        $project = $project->id() === null ? $project->withId($this->nextId++) : $project;
        $this->items[$project->id()] = clone $project;
        return $project;
    }

    public function delete(int $id): void { unset($this->items[$id]); }
}
