<?php

declare(strict_types=1);

namespace App\Job\Application;

use App\Job\Domain\Job;
use App\Job\Domain\JobRepository;

final class CreateJob
{
    public function __construct(private readonly JobRepository $jobs)
    {
    }

    public function execute(JobInput $input): Job
    {
        return $this->jobs->save(Job::create($input->title, $input->description, $input->months));
    }
}
