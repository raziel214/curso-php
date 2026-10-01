<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;

final class UserModel extends Model
{
    protected $table = 'users';
    protected $fillable = ['email', 'password'];
    protected $hidden = ['password'];
}
