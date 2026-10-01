<?php

declare(strict_types=1);

namespace App\Job\Infrastructure\Persistence;

use App\Job\Domain\Job;
use App\Job\Domain\JobRepository;

final class EloquentJobRepository implements JobRepository
{
    public function all(): array
    {
        return JobModel::query()->orderByDesc('id')->get()
            ->map(fn (JobModel $m) => $this->toDomain($m))
            ->values()->all();
    }

    public function find(int $id): ?Job
    {
        $model = JobModel::find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function save(Job $job): Job
    {
        $model = $job->id() !== null ? JobModel::findOrFail($job->id()) : new JobModel();
        $model->fill([
            'title' => $job->title(),
            'description' => $job->description(),
            'months' => $job->months(),
        ])->save();

        return $job->withId((int) $model->id);
    }

    public function delete(int $id): void
    {
        JobModel::destroy($id);
    }

    private function toDomain(JobModel $m): Job
    {
        return new Job((int) $m->id, (string) $m->title, (string) $m->description, (int) $m->months);
    }
}
