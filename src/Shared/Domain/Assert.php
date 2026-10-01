<?php

declare(strict_types=1);

namespace App\Shared\Domain;

/** Pequeñas guardas para proteger las invariantes de las entidades. */
final class Assert
{
    public static function notBlank(string $value, string $field, int $max = 255): string
    {
        $value = trim($value);
        if ($value === '') {
            throw ValidationException::single($field, sprintf('El campo "%s" es obligatorio.', $field));
        }
        if (mb_strlen($value) > $max) {
            throw ValidationException::single($field, sprintf('El campo "%s" admite máximo %d caracteres.', $field, $max));
        }
        return $value;
    }
}
