<?php

declare(strict_types=1);

namespace Tests\Unit\User;

use App\Shared\Domain\ValidationException;
use App\User\Application\{AuthenticateUser, CreateUser, DeleteUser, GetUser, ListUsers, UpdateUser, UserInput};
use App\User\Domain\InvalidCredentials;
use PHPUnit\Framework\TestCase;
use Tests\Support\{FakePasswordHasher, InMemoryUserRepository};

final class UserCrudTest extends TestCase
{
    private InMemoryUserRepository $repo;
    private FakePasswordHasher $hasher;

    protected function setUp(): void
    {
        $this->repo = new InMemoryUserRepository();
        $this->hasher = new FakePasswordHasher();
    }

    private function create(string $email, string $password = 'secreto123'): \App\User\Domain\User
    {
        return (new CreateUser($this->repo, $this->hasher))->execute(new UserInput($email, $password));
    }

    public function testCreateNormalizesEmailAndHashesPassword(): void
    {
        $user = $this->create('  Admin@Mail.com ');
        self::assertSame('admin@mail.com', $user->email());
        self::assertSame('hashed:secreto123', $user->passwordHash());
    }

    public function testRejectsDuplicateEmail(): void
    {
        $this->create('a@mail.com');
        $this->expectException(ValidationException::class);
        $this->create('A@mail.com');
    }

    public function testRejectsShortPasswordAndInvalidEmail(): void
    {
        try {
            $this->create('a@mail.com', 'corta');
            self::fail('Debió fallar por contraseña corta');
        } catch (ValidationException $e) {
            self::assertArrayHasKey('password', $e->errors());
        }
        $this->expectException(ValidationException::class);
        $this->create('no-es-email');
    }

    public function testUpdateKeepsPasswordWhenEmpty(): void
    {
        $user = $this->create('a@mail.com');
        $update = new UpdateUser($this->repo, $this->hasher, new GetUser($this->repo));

        $updated = $update->execute($user->id(), new UserInput('b@mail.com', ''));
        self::assertSame('b@mail.com', $updated->email());
        self::assertSame('hashed:secreto123', $updated->passwordHash());

        $updated = $update->execute($user->id(), new UserInput('b@mail.com', 'nuevaClave1'));
        self::assertSame('hashed:nuevaClave1', $updated->passwordHash());
    }

    public function testUpdateRejectsEmailOfAnotherUser(): void
    {
        $this->create('a@mail.com');
        $b = $this->create('b@mail.com');
        $this->expectException(ValidationException::class);
        (new UpdateUser($this->repo, $this->hasher, new GetUser($this->repo)))->execute($b->id(), new UserInput('a@mail.com'));
    }

    public function testCannotDeleteYourself(): void
    {
        $a = $this->create('a@mail.com');
        $b = $this->create('b@mail.com');
        $delete = new DeleteUser($this->repo, new GetUser($this->repo));

        $delete->execute($b->id(), currentUserId: $a->id());
        self::assertCount(1, (new ListUsers($this->repo))->execute());

        $this->expectException(ValidationException::class);
        $delete->execute($a->id(), currentUserId: $a->id());
    }

    public function testAuthenticate(): void
    {
        $this->create('a@mail.com');
        $auth = new AuthenticateUser($this->repo, $this->hasher);

        self::assertSame('a@mail.com', $auth->execute('A@mail.com', 'secreto123')->email());

        $this->expectException(InvalidCredentials::class);
        $auth->execute('a@mail.com', 'incorrecta');
    }
}
