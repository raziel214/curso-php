<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Job\Domain\Job;
use App\Job\Domain\JobRepository;

final class InMemoryJobRepository implements JobRepository
{
    /** @var array<int, Job> */
    private array $items = [];
    private int $nextId = 1;

    public function all(): array { return array_values($this->items); }
    public function find(int $id): ?Job { return isset($this->items[$id]) ? clone $this->items[$id] : null; }

    public function save(Job $job): Job
    {
        $job = $job->id() === null ? $job->withId($this->nextId++) : $job;
        $this->items[$job->id()] = clone $job;
        return $job;
    }

    public function delete(int $id): void { unset($this->items[$id]); }
}
