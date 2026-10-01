<?php

declare(strict_types=1);

namespace App\Job\Application;

use App\Job\Domain\Job;
use App\Job\Domain\JobRepository;

final class UpdateJob
{
    public function __construct(
        private readonly JobRepository $jobs,
        private readonly GetJob $getJob,
    ) {
    }

    public function execute(int $id, JobInput $input): Job
    {
        $job = $this->getJob->execute($id);
        $job->change($input->title, $input->description, $input->months);

        return $this->jobs->save($job);
    }
}
