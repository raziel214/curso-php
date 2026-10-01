<?php

declare(strict_types=1);

namespace App\User\Application;

use App\Shared\Domain\NotFoundException;
use App\User\Domain\User;
use App\User\Domain\UserRepository;

final class GetUser
{
    public function __construct(private readonly UserRepository $users)
    {
    }

    public function execute(int $id): User
    {
        return $this->users->find($id) ?? throw NotFoundException::of('User', $id);
    }
}
