<?php

declare(strict_types=1);

namespace App\Project\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;

final class ProjectModel extends Model
{
    protected $table = 'projects';
    protected $fillable = ['title', 'description', 'technologies'];
    protected $casts = ['technologies' => 'array'];
}
