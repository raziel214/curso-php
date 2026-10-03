<?php

declare(strict_types=1);

namespace Tests\Unit\Project;

use App\Project\Application\{CreateProject, DeleteProject, GetProject, ListProjects, ProjectInput, UpdateProject};
use App\Shared\Domain\{NotFoundException, ValidationException};
use PHPUnit\Framework\TestCase;
use Tests\Support\InMemoryProjectRepository;

final class ProjectCrudTest extends TestCase
{
    public function testFullCrud(): void
    {
        $repo = new InMemoryProjectRepository();
        $get = new GetProject($repo);

        $p = (new CreateProject($repo))->execute(ProjectInput::fromArray([
            'title' => 'Portafolio', 'description' => 'Sitio', 'technologies' => 'PHP, Twig, , PHP',
        ]));
        self::assertSame(['PHP', 'Twig'], $p->technologies());

        (new UpdateProject($repo, $get))->execute($p->id(), new ProjectInput('Portafolio v2', 'Sitio', ['MySQL']));
        self::assertSame('Portafolio v2', $get->execute($p->id())->title());
        self::assertCount(1, (new ListProjects($repo))->execute());

        (new DeleteProject($repo, $get))->execute($p->id());
        $this->expectException(NotFoundException::class);
        $get->execute($p->id());
    }

    public function testRejectsBlankDescription(): void
    {
        $this->expectException(ValidationException::class);
        (new CreateProject(new InMemoryProjectRepository()))->execute(new ProjectInput('t', ''));
    }
}
