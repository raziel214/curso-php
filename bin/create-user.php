<?php

declare(strict_types=1);

/* Uso: php bin/create-user.php admin@correo.com "clave-segura" */

use App\Shared\Domain\ValidationException;
use App\User\Application\{CreateUser, UserInput};

$container = require __DIR__ . '/../config/bootstrap.php';

[, $email, $password] = $argv + [null, null, null];
if (!$email || !$password) {
    fwrite(STDERR, "Uso: php bin/create-user.php <email> <password>\n");
    exit(1);
}

try {
    $user = $container->get(CreateUser::class)->execute(new UserInput($email, $password));
    echo "Usuario #{$user->id()} ({$user->email()}) creado.\n";
} catch (ValidationException $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}
