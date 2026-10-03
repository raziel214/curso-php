<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Security;

use App\User\Domain\PasswordHasher;

final class NativePasswordHasher implements PasswordHasher
{
    public function hash(string $plain): string
    {
        return password_hash($plain, PASSWORD_DEFAULT);
    }

    public function verify(string $plain, string $hash): bool
    {
        return password_verify($plain, $hash);
    }
}
