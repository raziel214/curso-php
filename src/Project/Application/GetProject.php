<?php

declare(strict_types=1);

namespace App\Project\Application;

use App\Project\Domain\Project;
use App\Project\Domain\ProjectRepository;
use App\Shared\Domain\NotFoundException;

final class GetProject
{
    public function __construct(private readonly ProjectRepository $projects)
    {
    }

    public function execute(int $id): Project
    {
        return $this->projects->find($id) ?? throw NotFoundException::of('Project', $id);
    }
}
