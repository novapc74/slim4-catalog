<?php

namespace App\Service\Command;

use GuzzleHttp\Client;
use App\Models\Category;
use Illuminate\Support\Str;
use GuzzleHttp\Exception\GuzzleException;

class CategoriesSyncCommandService
{
    private const ROOT_URI = '/peckingorder';
    private const CATEGORY_TITLE = 'Наименование';
    private const CATEGORY_ID = 'УникальныйИдентификатор';
    private const PARENT_CATEGORY_ID = 'РодительУникальныйИдентификатор';
    private const MAIN_CATEGORY_IDENTIFIER = '00000000-0000-0000-0000-000000000000';
    private static array $slugCollection = [];
    private static mixed $error = null;

    private Client $client;

    public function __construct()
    {
        $rootUri = env('ROOT_SERVER') . self::ROOT_URI;
        $this->client = new Client(['base_uri' => $rootUri]);
    }

    public static function init(): self
    {
        return new self();
    }

    /**
     * @throws GuzzleException
     */
    public function execute(bool $fromFile = false): array
    {
        if ($fromFile) {
            $file = file_get_contents(__DIR__ . '/../../../var/data/category.json');
            $data = json_decode($file, true);
        } else {
            if (!$data = self::getData()) {
                return self::$error;
            }
        }

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

    /**
     * @throws GuzzleException
     */
    private function getData(): ?array
    {
        $response = $this->client->request('GET');
        $responseCode = $response->getStatusCode();
        $responseContent = $response->getBody()->getContents();

        if ($responseCode !== 200) {
            self::$error['error'] = sprintf('Код ответа сервера: %s', $responseCode);
            return null;
        }

        $data = json_decode($responseContent, true);

        file_put_contents(__DIR__ . '/../../../var/data/category.json', json_encode($data['peckingorder'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        return $data['peckingorder'];
    }

}
