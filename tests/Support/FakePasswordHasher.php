<?php

declare(strict_types=1);

namespace Tests\Support;

use App\User\Domain\PasswordHasher;

final class FakePasswordHasher implements PasswordHasher
{
    public function hash(string $plain): string { return 'hashed:' . $plain; }
    public function verify(string $plain, string $hash): bool { return $hash === 'hashed:' . $plain; }
}
