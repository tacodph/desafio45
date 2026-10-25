<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Checkin extends Model
{
    protected $fillable = [
        'user_id',
        'day',
        'checked_in',
        'movement',
        'water',
        'sleep',
        'strength',
        'skill',
        'plus_count',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'checked_in' => 'boolean',
            'movement' => 'boolean',
            'water' => 'boolean',
            'sleep' => 'boolean',
            'strength' => 'boolean',
            'skill' => 'boolean',
            'plus_count' => 'integer',
            'notes' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
