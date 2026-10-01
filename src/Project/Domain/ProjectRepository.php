<?php

declare(strict_types=1);

namespace App\Project\Domain;

interface ProjectRepository
{
    /** @return list<Project> */
    public function all(): array;

    public function find(int $id): ?Project;

    public function save(Project $project): Project;

    public function delete(int $id): void;
}
