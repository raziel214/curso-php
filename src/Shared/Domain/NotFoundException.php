<?php

declare(strict_types=1);

namespace App\Shared\Domain;

final class NotFoundException extends DomainException
{
    public static function of(string $entity, int $id): self
    {
        return new self(sprintf('%s con id %d no existe.', $entity, $id));
    }
}
