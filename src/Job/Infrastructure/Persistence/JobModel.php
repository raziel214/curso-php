<?php

declare(strict_types=1);

namespace App\Job\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;

/** Modelo Eloquent: detalle de infraestructura, nunca sale de esta capa. */
final class JobModel extends Model
{
    protected $table = 'jobs';
    protected $fillable = ['title', 'description', 'months'];
}
