<?php

declare(strict_types=1);

namespace App\Shared\Domain;

final class ValidationException extends DomainException
{
    /** @param array<string, string> $errors campo => mensaje */
    public function __construct(private readonly array $errors)
    {
        parent::__construct(implode(' ', $errors));
    }

    /** @return array<string, string> */
    public function errors(): array
    {
        return $this->errors;
    }

    public static function single(string $field, string $message): self
    {
        return new self([$field => $message]);
    }
}
