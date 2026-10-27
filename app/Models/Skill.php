<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = [
        'category',
        'name',
        'position',
    ];

    /**
     * @return list<array{title: string, items: list<string>}>
     */
    public static function catalog(): array
    {
        return static::query()
            ->orderBy('position')
            ->get(['category', 'name'])
            ->groupBy('category')
            ->map(fn ($items, string $category) => [
                'title' => $category,
                'items' => $items->pluck('name')->values()->all(),
            ])
            ->values()
            ->all();
    }
}
