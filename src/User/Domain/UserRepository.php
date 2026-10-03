<?php

declare(strict_types=1);

namespace App\User\Domain;

interface UserRepository
{
    /** @return list<User> */
    public function all(): array;

    public function find(int $id): ?User;

    public function findByEmail(string $email): ?User;

    public function save(User $user): User;

    public function delete(int $id): void;
}
