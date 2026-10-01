<?php

declare(strict_types=1);

namespace Tests\Support;

use App\User\Domain\User;
use App\User\Domain\UserRepository;

final class InMemoryUserRepository implements UserRepository
{
    /** @var array<int, User> */
    private array $items = [];
    private int $nextId = 1;

    public function all(): array { return array_values($this->items); }
    public function find(int $id): ?User { return isset($this->items[$id]) ? clone $this->items[$id] : null; }

    public function findByEmail(string $email): ?User
    {
        foreach ($this->items as $user) {
            if ($user->email() === $email) {
                return clone $user;
            }
        }
        return null;
    }

    public function save(User $user): User
    {
        $user = $user->id() === null ? $user->withId($this->nextId++) : $user;
        $this->items[$user->id()] = clone $user;
        return $user;
    }

    public function delete(int $id): void { unset($this->items[$id]); }
}
