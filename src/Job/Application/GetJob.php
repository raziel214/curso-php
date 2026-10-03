<?php

declare(strict_types=1);

namespace App\Job\Application;

use App\Job\Domain\Job;
use App\Job\Domain\JobRepository;
use App\Shared\Domain\NotFoundException;

final class GetJob
{
    public function __construct(private readonly JobRepository $jobs)
    {
    }

    public function execute(int $id): Job
    {
        return $this->jobs->find($id) ?? throw NotFoundException::of('Job', $id);
    }
}
