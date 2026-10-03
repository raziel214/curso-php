<?php

declare(strict_types=1);

namespace App\Project\Application;

use App\Project\Domain\Project;
use App\Project\Domain\ProjectRepository;

final class CreateProject
{
    public function __construct(private readonly ProjectRepository $projects)
    {
    }

    public function execute(ProjectInput $input): Project
    {
        return $this->projects->save(Project::create($input->title, $input->description, $input->technologies));
    }
}
