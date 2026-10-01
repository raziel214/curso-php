<?php

declare(strict_types=1);

namespace App\User\Application;

use App\User\Domain\InvalidCredentials;
use App\User\Domain\PasswordHasher;
use App\User\Domain\User;
use App\User\Domain\UserRepository;

final class AuthenticateUser
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly PasswordHasher $hasher,
    ) {
    }

    public function execute(string $email, string $password): User
    {
        $user = $this->users->findByEmail(mb_strtolower(trim($email)));

        if ($user === null || !$this->hasher->verify($password, $user->passwordHash())) {
            throw new InvalidCredentials(); // mismo mensaje en ambos casos: no revela si el email existe
        }

        return $user;
    }
}
