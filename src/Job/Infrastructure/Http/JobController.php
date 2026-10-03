<?php

declare(strict_types=1);

namespace App\Job\Infrastructure\Http;

use App\Job\Application\{CreateJob, DeleteJob, GetJob, JobInput, ListJobs, UpdateJob};
use App\Shared\Domain\ValidationException;
use App\Shared\Infrastructure\Http\{Controller, Session};
use App\Shared\Infrastructure\View\TwigRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

final class JobController extends Controller
{
    public function __construct(
        TwigRenderer $view,
        Session $session,
        private readonly ListJobs $listJobs,
        private readonly GetJob $getJob,
        private readonly CreateJob $createJob,
        private readonly UpdateJob $updateJob,
        private readonly DeleteJob $deleteJob,
    ) {
        parent::__construct($view, $session);
    }

    public function index(Request $request): ResponseInterface
    {
        return $this->render('jobs/index.twig', ['jobs' => $this->listJobs->execute()]);
    }

    public function create(Request $request): ResponseInterface
    {
        return $this->form(null, ['title' => '', 'description' => '', 'months' => 0]);
    }

    public function store(Request $request): ResponseInterface
    {
        $data = $this->body($request);
        try {
            $this->createJob->execute(JobInput::fromArray($data));
        } catch (ValidationException $e) {
            return $this->form(null, $data, $e->errors());
        }

        return $this->redirect('/admin/jobs', 'Trabajo creado.');
    }

    public function edit(Request $request): ResponseInterface
    {
        $job = $this->getJob->execute($this->id($request));

        return $this->form($job->id(), [
            'title' => $job->title(),
            'description' => $job->description(),
            'months' => $job->months(),
        ]);
    }

    public function update(Request $request): ResponseInterface
    {
        $id = $this->id($request);
        $data = $this->body($request);
        try {
            $this->updateJob->execute($id, JobInput::fromArray($data));
        } catch (ValidationException $e) {
            return $this->form($id, $data, $e->errors());
        }

        return $this->redirect('/admin/jobs', 'Trabajo actualizado.');
    }

    public function destroy(Request $request): ResponseInterface
    {
        $this->deleteJob->execute($this->id($request));

        return $this->redirect('/admin/jobs', 'Trabajo eliminado.');
    }

    /** @param array<string, mixed> $values @param array<string, string> $errors */
    private function form(?int $id, array $values, array $errors = []): ResponseInterface
    {
        return $this->render('jobs/form.twig', compact('id', 'values', 'errors'), $errors ? 422 : 200);
    }
}
