<?php

declare(strict_types=1);

namespace App\User\Application;

final class UserInput
{
    public function __construct(
        public readonly string $email,
        public readonly string $password = '',
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self((string) ($data['email'] ?? ''), (string) ($data['password'] ?? ''));
    }
}
