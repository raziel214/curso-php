<?php

declare(strict_types=1);

namespace App\User\Application;

use App\Shared\Domain\ValidationException;
use App\User\Domain\PasswordHasher;
use App\User\Domain\PasswordPolicy;
use App\User\Domain\User;
use App\User\Domain\UserRepository;

final class UpdateUser
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly PasswordHasher $hasher,
        private readonly GetUser $getUser,
    ) {
    }

    /** Si `password` viene vacío se conserva la contraseña actual. */
    public function execute(int $id, UserInput $input): User
    {
        $user = $this->getUser->execute($id);
        $user->changeEmail($input->email);

        $owner = $this->users->findByEmail($user->email());
        if ($owner !== null && $owner->id() !== $id) {
            throw ValidationException::single('email', 'Ya existe un usuario con ese email.');
        }

        if ($input->password !== '') {
            PasswordPolicy::assert($input->password);
            $user->changePasswordHash($this->hasher->hash($input->password));
        }

        return $this->users->save($user);
    }
}
