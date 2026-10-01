<?php

declare(strict_types=1);

namespace App\User\Domain;

interface PasswordHasher
{
    public function hash(string $plain): string;

    public function verify(string $plain, string $hash): bool;
}
