<?php

declare(strict_types=1);

namespace App\Project\Application;

use App\Project\Domain\ProjectRepository;

final class DeleteProject
{
    public function __construct(
        private readonly ProjectRepository $projects,
        private readonly GetProject $getProject,
    ) {
    }

    public function execute(int $id): void
    {
        $this->getProject->execute($id);
        $this->projects->delete($id);
    }
}
