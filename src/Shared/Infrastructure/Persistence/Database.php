<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

final class Database
{
    /** @param array<string, string|null> $env */
    public static function boot(array $env): Capsule
    {
        $driver = $env['DB_DRIVER'] ?? 'mysql';

        $connection = $driver === 'sqlite'
            ? ['driver' => 'sqlite', 'database' => $env['DB_DATABASE'] ?? ':memory:', 'prefix' => '']
            : [
                'driver' => $driver,
                'host' => $env['DB_HOST'] ?? '127.0.0.1',
                'port' => $env['DB_PORT'] ?? '3306',
                'database' => $env['DB_DATABASE'] ?? 'cursophp',
                'username' => $env['DB_USERNAME'] ?? 'root',
                'password' => $env['DB_PASSWORD'] ?? '',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
            ];

        $capsule = new Capsule();
        $capsule->addConnection($connection);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        return $capsule;
    }

    /** Crea las tablas si no existen (idempotente). */
    public static function migrate(): void
    {
        $schema = Capsule::schema();

        if (!$schema->hasTable('jobs')) {
            $schema->create('jobs', function (Blueprint $t): void {
                $t->increments('id');
                $t->string('title');
                $t->text('description');
                $t->unsignedInteger('months')->default(0);
                $t->timestamps();
            });
        } elseif (!$schema->hasColumn('jobs', 'months')) {
            $schema->table('jobs', fn (Blueprint $t) => $t->unsignedInteger('months')->default(0));
        }

        if (!$schema->hasTable('projects')) {
            $schema->create('projects', function (Blueprint $t): void {
                $t->increments('id');
                $t->string('title');
                $t->text('description');
                $t->text('technologies')->nullable();
                $t->timestamps();
            });
        } elseif (!$schema->hasColumn('projects', 'technologies')) {
            $schema->table('projects', fn (Blueprint $t) => $t->text('technologies')->nullable());
        }

        if (!$schema->hasTable('users')) {
            $schema->create('users', function (Blueprint $t): void {
                $t->increments('id');
                $t->string('email')->unique();
                $t->string('password');
                $t->timestamps();
            });
        }
    }
}
