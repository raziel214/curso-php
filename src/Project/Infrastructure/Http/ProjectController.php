<?php

declare(strict_types=1);

namespace App\Project\Infrastructure\Http;

use App\Project\Application\{CreateProject, DeleteProject, GetProject, ListProjects, ProjectInput, UpdateProject};
use App\Shared\Domain\ValidationException;
use App\Shared\Infrastructure\Http\{Controller, Session};
use App\Shared\Infrastructure\View\TwigRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

final class ProjectController extends Controller
{
    public function __construct(
        TwigRenderer $view,
        Session $session,
        private readonly ListProjects $listProjects,
        private readonly GetProject $getProject,
        private readonly CreateProject $createProject,
        private readonly UpdateProject $updateProject,
        private readonly DeleteProject $deleteProject,
    ) {
        parent::__construct($view, $session);
    }

    public function index(Request $request): ResponseInterface
    {
        return $this->render('projects/index.twig', ['projects' => $this->listProjects->execute()]);
    }

    public function create(Request $request): ResponseInterface
    {
        return $this->form(null, ['title' => '', 'description' => '', 'technologies' => '']);
    }

    public function store(Request $request): ResponseInterface
    {
        $data = $this->body($request);
        try {
            $this->createProject->execute(ProjectInput::fromArray($data));
        } catch (ValidationException $e) {
            return $this->form(null, $data, $e->errors());
        }

        return $this->redirect('/admin/projects', 'Proyecto creado.');
    }

    public function edit(Request $request): ResponseInterface
    {
        $project = $this->getProject->execute($this->id($request));

        return $this->form($project->id(), [
            'title' => $project->title(),
            'description' => $project->description(),
            'technologies' => implode(', ', $project->technologies()),
        ]);
    }

    public function update(Request $request): ResponseInterface
    {
        $id = $this->id($request);
        $data = $this->body($request);
        try {
            $this->updateProject->execute($id, ProjectInput::fromArray($data));
        } catch (ValidationException $e) {
            return $this->form($id, $data, $e->errors());
        }

        return $this->redirect('/admin/projects', 'Proyecto actualizado.');
    }

    public function destroy(Request $request): ResponseInterface
    {
        $this->deleteProject->execute($this->id($request));

        return $this->redirect('/admin/projects', 'Proyecto eliminado.');
    }

    /** @param array<string, mixed> $values @param array<string, string> $errors */
    private function form(?int $id, array $values, array $errors = []): ResponseInterface
    {
        return $this->render('projects/form.twig', compact('id', 'values', 'errors'), $errors ? 422 : 200);
    }
}
