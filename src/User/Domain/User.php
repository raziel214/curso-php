<?php

declare(strict_types=1);

namespace App\User\Domain;

use App\Shared\Domain\ValidationException;

/** Entidad de dominio: usuario administrador. Solo conoce el hash, nunca la clave plana. */
final class User
{
    private string $email;

    public function __construct(
        private ?int $id,
        string $email,
        private string $passwordHash,
    ) {
        $this->changeEmail($email);
    }

    public static function register(string $email, string $passwordHash): self
    {
        return new self(null, $email, $passwordHash);
    }

    public function changeEmail(string $email): void
    {
        $email = mb_strtolower(trim($email));
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw ValidationException::single('email', 'El email no es válido.');
        }
        $this->email = $email;
    }

    public function changePasswordHash(string $hash): void
    {
        $this->passwordHash = $hash;
    }

    public function withId(int $id): self
    {
        $copy = clone $this;
        $copy->id = $id;
        return $copy;
    }

    public function id(): ?int { return $this->id; }
    public function email(): string { return $this->email; }
    public function passwordHash(): string { return $this->passwordHash; }
}
