<?php

declare(strict_types=1);

use App\Shared\Infrastructure\Persistence\Database;

require __DIR__ . '/../config/bootstrap.php';

Database::migrate();

echo "Tablas jobs, projects y users listas.\n";
