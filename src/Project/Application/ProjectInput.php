<?php

declare(strict_types=1);

namespace App\Project\Application;

final class ProjectInput
{
    /** @param list<string> $technologies */
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly array $technologies = [],
    ) {
    }

    /** @param array<string, mixed> $data  `technologies` llega como texto separado por comas */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['title'] ?? ''),
            (string) ($data['description'] ?? ''),
            explode(',', (string) ($data['technologies'] ?? '')),
        );
    }
}
