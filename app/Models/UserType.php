<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserType extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public static function idFor(UserRole $role): int
    {
        return (int) static::query()->where('name', $role->value)->value('id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
