<?php

namespace App\Service\Command\CategorySync;

use Illuminate\Support\Str;

class CategoryDto
{
    private const CATEGORY_TITLE = 'Наименование';
    private const CATEGORY_ID = 'УникальныйИдентификатор';
    private const PARENT_CATEGORY_ID = 'РодительУникальныйИдентификатор';
    private const MAIN_CATEGORY_IDENTIFIER = '00000000-0000-0000-0000-000000000000';
    private static array $slugCollection = [];


    public static function makeCategory(array $data): array
    {
        $parentId = match ($data[self::PARENT_CATEGORY_ID]) {
            self::MAIN_CATEGORY_IDENTIFIER => null,
            default => $data[self::PARENT_CATEGORY_ID],
        };

        return [
            'id' => $data[self::CATEGORY_ID],
            'title' => $data[self::CATEGORY_TITLE],
            'slug' => self::getSlug($data[self::CATEGORY_TITLE]),
            'parent_category_id' => $parentId,
        ];
    }

    public static function getSlug(string $title): string
    {
        $slug = Str::slug($title);
        if (!in_array($slug, self::$slugCollection)) {
            self::$slugCollection[] = $slug;
            return $slug;
        }

        $i = 1;
        while (in_array($slug, self::$slugCollection)) {
            $slug = $slug . '-' . $i;
            $i++;
        }

        self::$slugCollection[] = $slug;

        return $slug;
    }
}
