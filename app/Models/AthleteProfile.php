<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AthleteProfile extends Model
{
    protected $fillable = [
        'user_id',
        'movement_level',
        'movement_minutes',
        'water_liters',
        'sleep_goal',
        'skill',
        'strength_goal',
        'test_1',
        'test_15',
        'test_30',
        'test_45',
        'penalty',
        'penalty_donated_on',
        'penalty_note',
    ];

    protected function casts(): array
    {
        return [
            'penalty' => 'boolean',
            'penalty_donated_on' => 'date',
            'movement_minutes' => 'integer',
            'water_liters' => 'decimal:1',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
