<?php

declare(strict_types=1);

namespace App\Job\Application;

/** DTO de entrada para crear/actualizar un Job. */
final class JobInput
{
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly int $months = 0,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['title'] ?? ''),
            (string) ($data['description'] ?? ''),
            (int) ($data['months'] ?? 0),
        );
    }
}
