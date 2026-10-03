<?php

declare(strict_types=1);

namespace Tests\Unit\Job;

use App\Job\Application\{CreateJob, DeleteJob, GetJob, JobInput, ListJobs, UpdateJob};
use App\Job\Domain\Job;
use App\Shared\Domain\{NotFoundException, ValidationException};
use PHPUnit\Framework\TestCase;
use Tests\Support\InMemoryJobRepository;

final class JobCrudTest extends TestCase
{
    private InMemoryJobRepository $repo;
    private GetJob $get;

    protected function setUp(): void
    {
        $this->repo = new InMemoryJobRepository();
        $this->get = new GetJob($this->repo);
    }

    public function testCreateListUpdateDelete(): void
    {
        $job = (new CreateJob($this->repo))->execute(new JobInput('Dev PHP', 'Backend', 14));
        self::assertSame(1, $job->id());
        self::assertCount(1, (new ListJobs($this->repo))->execute());

        (new UpdateJob($this->repo, $this->get))->execute(1, new JobInput('Arquitecto', 'Soluciones', 24));
        $updated = $this->get->execute(1);
        self::assertSame('Arquitecto', $updated->title());
        self::assertSame(24, $updated->months());

        (new DeleteJob($this->repo, $this->get))->execute(1);
        self::assertSame([], (new ListJobs($this->repo))->execute());
    }

    public function testRejectsBlankTitle(): void
    {
        $this->expectException(ValidationException::class);
        (new CreateJob($this->repo))->execute(new JobInput('  ', 'desc'));
    }

    public function testRejectsNegativeMonths(): void
    {
        $this->expectException(ValidationException::class);
        Job::create('t', 'd', -1);
    }

    public function testUpdateMissingJobThrowsNotFound(): void
    {
        $this->expectException(NotFoundException::class);
        (new UpdateJob($this->repo, $this->get))->execute(99, new JobInput('t', 'd'));
    }

    public function testDurationAsString(): void
    {
        self::assertSame('5 meses', Job::create('t', 'd', 5)->durationAsString());
        self::assertSame('2 años', Job::create('t', 'd', 24)->durationAsString());
        self::assertSame('1 año y 1 mes', Job::create('t', 'd', 13)->durationAsString());
    }
}
