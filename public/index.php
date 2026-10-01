<?php

declare(strict_types=1);

use App\Shared\Infrastructure\Http\Kernel;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;

// Servidor embebido de PHP: dejar que sirva los archivos estáticos.
if (PHP_SAPI === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

$container = require __DIR__ . '/../config/bootstrap.php';

$response = $container->get(Kernel::class)->handle(ServerRequestFactory::fromGlobals());

(new SapiEmitter())->emit($response);
