<?php

declare(strict_types=1);

namespace App\Job\Application;

use App\Job\Domain\JobRepository;

final class DeleteJob
{
    public function __construct(
        private readonly JobRepository $jobs,
        private readonly GetJob $getJob,
    ) {
    }

    public function execute(int $id): void
    {
        $this->getJob->execute($id); // lanza NotFoundException si no existe
        $this->jobs->delete($id);
    }
}
