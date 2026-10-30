<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ChallengePayment extends Model
{
    public const PENDING = 'PENDING';

    public const RECEIVED = 'RECEIVED';

    public const CONFIRMED = 'CONFIRMED';

    public const OVERDUE = 'OVERDUE';

    public const REFUNDED = 'REFUNDED';

    public const DELETED = 'DELETED';

    /** Status que liberam o acesso ao desafio. */
    public const PAID_STATUSES = [self::RECEIVED, self::CONFIRMED];

    protected $fillable = [
        'uuid',
        'user_id',
        'asaas_payment_id',
        'status',
        'billing_type',
        'value',
        'due_date',
        'invoice_url',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'due_date' => 'date',
            'confirmed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $payment) {
            $payment->uuid ??= (string) Str::uuid();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Cobrança ainda utilizável: pendente e dentro do prazo. */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::PENDING)
            ->whereDate('due_date', '>=', today());
    }

    public function isPaid(): bool
    {
        return in_array($this->status, self::PAID_STATUSES, true);
    }
}
