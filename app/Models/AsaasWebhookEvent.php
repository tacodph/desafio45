<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsaasWebhookEvent extends Model
{
    protected $fillable = [
        'event_id',
        'event',
        'payload',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'processed_at' => 'datetime',
        ];
    }
}
