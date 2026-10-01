<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Persistence;

use App\User\Domain\User;
use App\User\Domain\UserRepository;

final class EloquentUserRepository implements UserRepository
{
    public function all(): array
    {
        return UserModel::query()->orderBy('email')->get()
            ->map(fn (UserModel $m) => $this->toDomain($m))
            ->values()->all();
    }

    public function find(int $id): ?User
    {
        $model = UserModel::find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $model = UserModel::where('email', $email)->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function save(User $user): User
    {
        $model = $user->id() !== null ? UserModel::findOrFail($user->id()) : new UserModel();
        $model->fill(['email' => $user->email(), 'password' => $user->passwordHash()])->save();

        return $user->withId((int) $model->id);
    }

    public function delete(int $id): void
    {
        UserModel::destroy($id);
    }

    private function toDomain(UserModel $m): User
    {
        return new User((int) $m->id, (string) $m->email, (string) $m->password);
    }
}
