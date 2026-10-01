<?php

declare(strict_types=1);

namespace App\User\Application;

use App\User\Domain\User;
use App\User\Domain\UserRepository;

final class ListUsers
{
    public function __construct(private readonly UserRepository $users)
    {
    }

    /** @return list<User> */
    public function execute(): array
    {
        return $this->users->all();
    }
}
