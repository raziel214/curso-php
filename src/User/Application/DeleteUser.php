<?php

declare(strict_types=1);

namespace App\User\Application;

use App\Shared\Domain\ValidationException;
use App\User\Domain\UserRepository;

final class DeleteUser
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly GetUser $getUser,
    ) {
    }

    public function execute(int $id, ?int $currentUserId = null): void
    {
        if ($id === $currentUserId) {
            throw ValidationException::single('id', 'No puedes eliminar tu propio usuario.');
        }
        $this->getUser->execute($id);
        $this->users->delete($id);
    }
}
