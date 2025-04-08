<?php

namespace App\Service\Command\CategorySync;

use App\Models\Category;

class CategoryUpdate
{
    private const FILE_PATH = __DIR__ . '/../../../../var/data/categories.json';
    private static array $idCategories = [];
    private static array $resolvedCategoriesId = [];
    private static array $sortedCategories = [];

    public static function execute(): array
    {
        $data = self::getFileData();

        $categories = self::getCategories($data);

        self::sortCategories($categories);

        $countCategory = Category::upsertCategory(self::$sortedCategories);

        return [
            'updatedCategories' => $countCategory,
            'amountCategory' => count(self::$sortedCategories),
        ];
    }

    public static function getCategories(array $data): array
    {
        $categories = [];
        foreach ($data as $categoryItem) {
            $categories[] = CategoryAdapter::makeCategory($categoryItem);
        }

        return $categories;
    }

    private static function getFileData(): array
    {
        $file = file_get_contents(self::FILE_PATH);
        return json_decode($file, true);
    }

    private static function setCategoriesById(array $categories): void
    {
        foreach ($categories as $category) {
            self::$idCategories[$category['id']] = $category;
        }
    }

    private static function addMainCategories(array $categories): void
    {
        foreach ($categories as $category) {
            $isNewParentCategory = !$category['parent_category_id'] && !in_array($category['id'], self::$resolvedCategoriesId);

            if ($isNewParentCategory) {
                self::$resolvedCategoriesId[] = $category['id'];
                self::$sortedCategories[] = $category;

                unset(self::$idCategories[$category['id']]);
            }

        }
    }

    private static function addChildCategories(array $categories): void
    {
        while (count(self::$idCategories) > 0) {
            foreach ($categories as $category) {
                $hasParentCategory = in_array($category['parent_category_id'], self::$resolvedCategoriesId);
                $isNewChildCategory = !in_array($category['id'], self::$resolvedCategoriesId);
                $isValidChildCategory = $hasParentCategory && $isNewChildCategory;

                if ($isValidChildCategory) {
                    self::$sortedCategories[] = $category;
                    self::$resolvedCategoriesId[] = $category['id'];

                    unset(self::$idCategories[$category['id']]);
                }
            }
        }
    }

    private static function sortCategories(array $categories): void
    {
        self::setCategoriesById($categories);
        self::addMainCategories($categories);
        self::addChildCategories($categories);
    }
}
