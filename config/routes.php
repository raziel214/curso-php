<?php

declare(strict_types=1);

use App\Job\Infrastructure\Http\JobController;
use App\Project\Infrastructure\Http\ProjectController;
use App\Shared\Infrastructure\Http\HomeController;
use App\User\Infrastructure\Http\{AuthController, UserController};
use Aura\Router\RouterContainer;

return static function (RouterContainer $router): RouterContainer {
    $map = $router->getMap();

    // Público
    $map->get('home', '/', [HomeController::class, 'resume']);
    $map->get('login', '/login', [AuthController::class, 'showLogin']);
    $map->post('login.submit', '/login', [AuthController::class, 'login']);
    $map->post('logout', '/logout', [AuthController::class, 'logout']);

    // Panel protegido
    $map->attach('admin.', '/admin', function ($map): void {
        $map->auth(true)->tokens(['id' => '\d+']);

        $map->get('dashboard', '', [HomeController::class, 'dashboard']);

        $crud = [
            'jobs' => JobController::class,
            'projects' => ProjectController::class,
            'users' => UserController::class,
        ];
        foreach ($crud as $resource => $controller) {
            $map->get("$resource.index", "/$resource", [$controller, 'index']);
            $map->get("$resource.create", "/$resource/new", [$controller, 'create']);
            $map->post("$resource.store", "/$resource", [$controller, 'store']);
            $map->get("$resource.edit", "/$resource/{id}/edit", [$controller, 'edit']);
            $map->post("$resource.update", "/$resource/{id}", [$controller, 'update']);
            $map->post("$resource.destroy", "/$resource/{id}/delete", [$controller, 'destroy']);
        }
    });

    return $router;
};
