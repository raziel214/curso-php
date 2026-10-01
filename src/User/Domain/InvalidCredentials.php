<?php

declare(strict_types=1);

namespace App\User\Domain;

use App\Shared\Domain\DomainException;

final class InvalidCredentials extends DomainException
{
    public function __construct()
    {
        parent::__construct('Credenciales inválidas.');
    }
}
