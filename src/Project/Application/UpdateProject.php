<?php

declare(strict_types=1);

namespace App\Project\Application;

use App\Project\Domain\Project;
use App\Project\Domain\ProjectRepository;

final class UpdateProject
{
    public function __construct(
        private readonly ProjectRepository $projects,
        private readonly GetProject $getProject,
    ) {
    }

    public function execute(int $id, ProjectInput $input): Project
    {
        $project = $this->getProject->execute($id);
        $project->change($input->title, $input->description, $input->technologies);

        return $this->projects->save($project);
    }
}
