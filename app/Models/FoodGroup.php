<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FoodGroup extends Model
{
    protected $fillable = [
        'category',
        'name',
        'notes',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'notes' => 'array',
            'position' => 'integer',
        ];
    }

    public function foods(): HasMany
    {
        return $this->hasMany(Food::class)->orderBy('position');
    }

    /** @return list<array{title: string, groups: list<array{title: string, items: list<string>, notes: list<string>}>}> */
    public static function catalog(): array
    {
        return static::query()
            ->with('foods')
            ->orderBy('position')
            ->orderBy('id')
            ->get()
            ->groupBy('category')
            ->map(fn ($groups, $category) => [
                'title' => $category,
                'groups' => $groups->map(fn (self $group) => [
                    'title' => $group->name,
                    'items' => $group->foods->where('active', true)->pluck('name')->values()->all(),
                    'notes' => $group->notes ?? [],
                ])->filter(fn (array $group) => $group['items'] !== [] || $group['notes'] !== [])->values()->all(),
            ])
            ->values()
            ->all();
    }
}
