<?php

namespace App\Service\Command\SyncData\CategorySync;

use App\Service\Command\Interface\GenerateSlugInterface;
use App\Traits\GenerateSlagTrait;

class CategoryDto implements GenerateSlugInterface
{
    use GenerateSlagTrait;
    private const CATEGORY_TITLE = 'Наименование';
    private const CATEGORY_ID = 'УникальныйИдентификатор';
    private const PARENT_CATEGORY_ID = 'РодительУникальныйИдентификатор';
    private const MAIN_CATEGORY_IDENTIFIER = '00000000-0000-0000-0000-000000000000';


    public static function makeCategory(array $data): array
    {
        $parentId = match ($data[self::PARENT_CATEGORY_ID]) {
            self::MAIN_CATEGORY_IDENTIFIER => null,
            default => $data[self::PARENT_CATEGORY_ID],
        };

        return [
            'id' => $data[self::CATEGORY_ID],
            'title' => $data[self::CATEGORY_TITLE],
            'slug' => self::generateSlug($data[self::CATEGORY_TITLE]),
            'parent_category_id' => $parentId,
        ];
    }
}
