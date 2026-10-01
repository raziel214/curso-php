<?php

declare(strict_types=1);

use App\Shared\Infrastructure\Persistence\Database;
use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

$root = dirname(__DIR__);

if (is_file($root . '/.env')) {
    Dotenv::createImmutable($root)->safeLoad();
}

$env = $_ENV + getenv() + [
    'APP_DEBUG' => 'false',
    'DB_DRIVER' => 'mysql',
];

if (($env['DB_DRIVER'] ?? '') === 'sqlite' && isset($env['DB_DATABASE']) && $env['DB_DATABASE'] !== ':memory:'
    && !str_starts_with($env['DB_DATABASE'], '/')) {
    $env['DB_DATABASE'] = $root . '/' . $env['DB_DATABASE'];
}

Database::boot($env);

return (require __DIR__ . '/container.php')($env, $root);
