<?php

declare(strict_types=1);

/*
 * Composition root: el ÚNICO lugar donde se conectan las interfaces
 * del dominio con sus implementaciones concretas de infraestructura.
 */

use App\Job\Application\{CreateJob, DeleteJob, GetJob, ListJobs, UpdateJob};
use App\Job\Domain\JobRepository;
use App\Job\Infrastructure\Http\JobController;
use App\Job\Infrastructure\Persistence\EloquentJobRepository;
use App\Project\Application\{CreateProject, DeleteProject, GetProject, ListProjects, UpdateProject};
use App\Project\Domain\ProjectRepository;
use App\Project\Infrastructure\Http\ProjectController;
use App\Project\Infrastructure\Persistence\EloquentProjectRepository;
use App\Shared\Infrastructure\Container;
use App\Shared\Infrastructure\Http\{HomeController, Kernel, Session};
use App\Shared\Infrastructure\View\TwigRenderer;
use App\User\Application\{AuthenticateUser, CreateUser, DeleteUser, GetUser, ListUsers, UpdateUser};
use App\User\Domain\{PasswordHasher, UserRepository};
use App\User\Infrastructure\Http\{AuthController, UserController};
use App\User\Infrastructure\Persistence\EloquentUserRepository;
use App\User\Infrastructure\Security\NativePasswordHasher;
use Aura\Router\RouterContainer;

return static function (array $env, string $root): Container {
    $c = new Container();
    $debug = filter_var($env['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN);

    // --- Infraestructura compartida
    $c->set(Session::class, fn () => new Session());
    $c->set(TwigRenderer::class, fn (Container $c) => new TwigRenderer($root . '/views', $c->get(Session::class), $debug));
    $c->set(RouterContainer::class, fn () => (require __DIR__ . '/routes.php')(new RouterContainer()));
    $c->set(Kernel::class, fn (Container $c) => new Kernel(
        $c->get(RouterContainer::class), $c, $c->get(Session::class), $c->get(TwigRenderer::class), $debug,
    ));

    // --- Puertos -> adaptadores
    $c->set(JobRepository::class, fn () => new EloquentJobRepository());
    $c->set(ProjectRepository::class, fn () => new EloquentProjectRepository());
    $c->set(UserRepository::class, fn () => new EloquentUserRepository());
    $c->set(PasswordHasher::class, fn () => new NativePasswordHasher());

    // --- Casos de uso: Job
    $c->set(ListJobs::class, fn (Container $c) => new ListJobs($c->get(JobRepository::class)));
    $c->set(GetJob::class, fn (Container $c) => new GetJob($c->get(JobRepository::class)));
    $c->set(CreateJob::class, fn (Container $c) => new CreateJob($c->get(JobRepository::class)));
    $c->set(UpdateJob::class, fn (Container $c) => new UpdateJob($c->get(JobRepository::class), $c->get(GetJob::class)));
    $c->set(DeleteJob::class, fn (Container $c) => new DeleteJob($c->get(JobRepository::class), $c->get(GetJob::class)));

    // --- Casos de uso: Project
    $c->set(ListProjects::class, fn (Container $c) => new ListProjects($c->get(ProjectRepository::class)));
    $c->set(GetProject::class, fn (Container $c) => new GetProject($c->get(ProjectRepository::class)));
    $c->set(CreateProject::class, fn (Container $c) => new CreateProject($c->get(ProjectRepository::class)));
    $c->set(UpdateProject::class, fn (Container $c) => new UpdateProject($c->get(ProjectRepository::class), $c->get(GetProject::class)));
    $c->set(DeleteProject::class, fn (Container $c) => new DeleteProject($c->get(ProjectRepository::class), $c->get(GetProject::class)));

    // --- Casos de uso: User
    $c->set(ListUsers::class, fn (Container $c) => new ListUsers($c->get(UserRepository::class)));
    $c->set(GetUser::class, fn (Container $c) => new GetUser($c->get(UserRepository::class)));
    $c->set(CreateUser::class, fn (Container $c) => new CreateUser($c->get(UserRepository::class), $c->get(PasswordHasher::class)));
    $c->set(UpdateUser::class, fn (Container $c) => new UpdateUser($c->get(UserRepository::class), $c->get(PasswordHasher::class), $c->get(GetUser::class)));
    $c->set(DeleteUser::class, fn (Container $c) => new DeleteUser($c->get(UserRepository::class), $c->get(GetUser::class)));
    $c->set(AuthenticateUser::class, fn (Container $c) => new AuthenticateUser($c->get(UserRepository::class), $c->get(PasswordHasher::class)));

    // --- Controladores
    $view = fn (Container $c) => [$c->get(TwigRenderer::class), $c->get(Session::class)];
    $c->set(HomeController::class, fn (Container $c) => new HomeController(...$view($c), listJobs: $c->get(ListJobs::class), listProjects: $c->get(ListProjects::class)));
    $c->set(JobController::class, fn (Container $c) => new JobController(
        ...$view($c),
        listJobs: $c->get(ListJobs::class), getJob: $c->get(GetJob::class), createJob: $c->get(CreateJob::class),
        updateJob: $c->get(UpdateJob::class), deleteJob: $c->get(DeleteJob::class),
    ));
    $c->set(ProjectController::class, fn (Container $c) => new ProjectController(
        ...$view($c),
        listProjects: $c->get(ListProjects::class), getProject: $c->get(GetProject::class), createProject: $c->get(CreateProject::class),
        updateProject: $c->get(UpdateProject::class), deleteProject: $c->get(DeleteProject::class),
    ));
    $c->set(UserController::class, fn (Container $c) => new UserController(
        ...$view($c),
        listUsers: $c->get(ListUsers::class), getUser: $c->get(GetUser::class), createUser: $c->get(CreateUser::class),
        updateUser: $c->get(UpdateUser::class), deleteUser: $c->get(DeleteUser::class),
    ));
    $c->set(AuthController::class, fn (Container $c) => new AuthController(...$view($c), authenticate: $c->get(AuthenticateUser::class)));

    return $c;
};
