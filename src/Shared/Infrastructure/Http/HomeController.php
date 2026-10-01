<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http;

use App\Job\Application\ListJobs;
use App\Project\Application\ListProjects;
use App\Shared\Infrastructure\View\TwigRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

/** Página pública (hoja de vida) y tablero del panel. */
final class HomeController extends Controller
{
    public function __construct(
        TwigRenderer $view,
        Session $session,
        private readonly ListJobs $listJobs,
        private readonly ListProjects $listProjects,
    ) {
        parent::__construct($view, $session);
    }

    public function resume(Request $request): ResponseInterface
    {
        return $this->render('resume/index.twig', [
            'name' => 'John Fredy Quimbaya Orozco',
            'jobs' => $this->listJobs->execute(),
            'projects' => $this->listProjects->execute(),
        ]);
    }

    public function dashboard(Request $request): ResponseInterface
    {
        return $this->render('admin/dashboard.twig', [
            'jobsCount' => count($this->listJobs->execute()),
            'projectsCount' => count($this->listProjects->execute()),
        ]);
    }
}
