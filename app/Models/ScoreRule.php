<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ScoreRule extends Model
{
    protected $fillable = [
        'key',
        'group',
        'label',
        'points',
        'sort',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'sort' => 'integer',
        ];
    }

    /** @return Collection<string, int> */
    public static function map(): Collection
    {
        return static::query()->pluck('points', 'key');
    }

    /** @return array{daily: Collection, bonus: Collection, dailyMax: int} */
    public static function grouped(): array
    {
        $all = static::query()->orderBy('sort')->get(['id', 'key', 'group', 'label', 'points']);

        return [
            'daily' => $all->where('group', 'daily')->values(),
            'bonus' => $all->where('group', 'bonus')->values(),
            'dailyMax' => (int) $all->where('group', 'daily')->sum('points'),
        ];
    }
}
