<?php

declare(strict_types=1);

namespace App\User\Application;

use App\Shared\Domain\ValidationException;
use App\User\Domain\PasswordHasher;
use App\User\Domain\PasswordPolicy;
use App\User\Domain\User;
use App\User\Domain\UserRepository;

final class CreateUser
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly PasswordHasher $hasher,
    ) {
    }

    public function execute(UserInput $input): User
    {
        PasswordPolicy::assert($input->password);
        $user = User::register($input->email, $this->hasher->hash($input->password));

        if ($this->users->findByEmail($user->email()) !== null) {
            throw ValidationException::single('email', 'Ya existe un usuario con ese email.');
        }

        return $this->users->save($user);
    }
}
