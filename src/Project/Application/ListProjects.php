<?php

declare(strict_types=1);

namespace App\Project\Application;

use App\Project\Domain\Project;
use App\Project\Domain\ProjectRepository;

final class ListProjects
{
    public function __construct(private readonly ProjectRepository $projects)
    {
    }

    /** @return list<Project> */
    public function execute(): array
    {
        return $this->projects->all();
    }
}
