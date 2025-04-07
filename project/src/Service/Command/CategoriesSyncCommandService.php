<?php

namespace App\Service\Command;

use App\Models\Category;
use Illuminate\Support\Str;
use GuzzleHttp\Exception\GuzzleException;

class CategoriesSyncCommandService
{
    private const CATEGORY_TITLE = 'Наименование';
    private const CATEGORY_ID = 'УникальныйИдентификатор';
    private const PARENT_CATEGORY_ID = 'РодительУникальныйИдентификатор';
    private const MAIN_CATEGORY_IDENTIFIER = '00000000-0000-0000-0000-000000000000';
    private static array $slugCollection = [];

    public function __construct()
    {
    }

    public static function init(): self
    {
        return new self();
    }

    /**
     * @throws GuzzleException
     */
    public function execute(): array
    {
        $file = file_get_contents(__DIR__ . '/../../../var/data/categories.json');
        $data = json_decode($file, true);

        $allCategories = self::getCategories($data);
        $idCategories = [];
        foreach ($allCategories as $category) {
            $idCategories[$category['id']] = $category;
        }

        $sortedCategories = [];
        $resolvedCategoriesId = [];

        // Наполняем массив категориями самого верхнего уровня.
        foreach ($allCategories as $category) {
            if (!$category['parent_category_id'] && !in_array($category['id'], $resolvedCategoriesId)) {
                $resolvedCategoriesId[] = $category['id'];
                $sortedCategories[] = $category;
                unset($idCategories[$category['id']]);
            }
        }

        // Наполняем массив дочерними категориями, при условии, что в массиве уже есть родительская категория.
        while (count($idCategories) > 0) {
            foreach ($allCategories as $category) {
                if (in_array($category['parent_category_id'], $resolvedCategoriesId) && !in_array($category['id'], $resolvedCategoriesId)) {
                    $sortedCategories[] = $category;
                    $resolvedCategoriesId[] = $category['id'];
                    unset($idCategories[$category['id']]);
                }
            }
        }

        // Смело создаем, обновляем - ведь у нас уже все категории на месте и отсортированы.
        Category::upsert($sortedCategories, ['id', 'slug']);

        return [
            'updatedCategories' => count($sortedCategories),
        ];
    }

    public static function getCategories(array $data): array
    {
        $categories = [];
        foreach ($data as $categoryItem) {
            $categories[] = self::categoryAdapter($categoryItem);
        }

        return $categories;
    }

    private static function categoryAdapter(array $data): array
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
