<?php

declare(strict_types=1);

namespace App\Job\Application;

use App\Job\Domain\Job;
use App\Job\Domain\JobRepository;

final class ListJobs
{
    public function __construct(private readonly JobRepository $jobs)
    {
    }

    /** @return list<Job> */
    public function execute(): array
    {
        return $this->jobs->all();
    }
}
