<?php

declare(strict_types=1);

namespace App\Job\Domain;

/** Puerto de salida: el dominio define el contrato, la infraestructura lo implementa. */
interface JobRepository
{
    /** @return list<Job> */
    public function all(): array;

    public function find(int $id): ?Job;

    /** Inserta o actualiza y devuelve la entidad con id asignado. */
    public function save(Job $job): Job;

    public function delete(int $id): void;
}
