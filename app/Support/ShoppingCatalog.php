<?php

namespace App\Support;

use App\Models\FoodGroup;

class ShoppingCatalog
{
    public static function import(string $markdown): void
    {
        $category = null;
        $groupName = '';
        $items = [];
        $notes = [];
        $position = 0;

        $flush = function () use (&$category, &$groupName, &$items, &$notes, &$position): void {
            if ($category === null || ($items === [] && $notes === [])) {
                $items = [];
                $notes = [];

                return;
            }

            $group = FoodGroup::query()->create([
                'category' => $category,
                'name' => $groupName,
                'notes' => $notes === [] ? null : $notes,
                'position' => ++$position,
            ]);

            foreach (array_values($items) as $index => $name) {
                $group->foods()->create([
                    'name' => $name,
                    'position' => $index + 1,
                ]);
            }

            $items = [];
            $notes = [];
        };

        foreach (preg_split("/\r\n|\n|\r/", $markdown) as $line) {
            if (str_starts_with($line, '## ')) {
                $title = trim(substr($line, 3));
                $flush();
                $category = preg_match('/^\d+\./', $title) ? $title : null;
                $groupName = '';

                continue;
            }

            if ($category === null) {
                continue;
            }

            if (str_starts_with($line, '### ')) {
                $flush();
                $groupName = trim(substr($line, 4));

                continue;
            }

            if (str_starts_with($line, '- [ ] ')) {
                $items[] = trim(substr($line, 6));

                continue;
            }

            $text = trim($line);
            if ($text === '' || $text === '---') {
                continue;
            }

            $text = preg_replace('/\*\*(.+?)\*\*/', '$1', $text) ?? $text;
            $text = trim($text, "* \t");
            $text = preg_replace('/^>\s*/', '', $text) ?? $text;

            if ($text !== '') {
                $notes[] = $text;
            }
        }

        $flush();
    }
}
