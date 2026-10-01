<?php

declare(strict_types=1);

namespace App\User\Domain;

use App\Shared\Domain\ValidationException;

final class PasswordPolicy
{
    public const MIN_LENGTH = 8;

    public static function assert(string $plain): void
    {
        if (mb_strlen($plain) < self::MIN_LENGTH) {
            throw ValidationException::single('password', sprintf('La contraseña debe tener al menos %d caracteres.', self::MIN_LENGTH));
        }
    }
}
