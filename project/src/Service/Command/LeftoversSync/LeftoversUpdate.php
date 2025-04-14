<?php

namespace App\Service\Command\LeftoversSync;

use Generator;
use App\Models\Product;
use App\Models\Leftover;

class LeftoversUpdate
{
    private const FILE_PATH = __DIR__ . '/../../../../var/data/leftovers.json';

    public static function execute(): int
    {
        $i = 0;
        foreach (self::getCollection() as $leftoverItem) {
            if (!$productId = Product::where('id', $leftoverItem['УникальныйИдентификатор'])?->value('id')) {
                continue;
            }

            $collection = [];
            foreach ($leftoverItem['bases'] ?? [] as $leftoverBase) {

                if (!$leftoverData = LeftoversDto::new($productId, $leftoverBase)->execute()) {
                    continue;
                }

                $collection[] = $leftoverData;
                $i++;
            }

            Leftover::upsertLeftovers($collection);
        }

        return $i;
    }

    private static function getCollection(): Generator
    {
        foreach (self::getDataFromFile() ?? [] as $item) {
            yield $item;
        }
    }

    private static function getDataFromFile(): ?array
    {
        if (!file_exists(self::FILE_PATH)) {
            return null;
        }

        $oldData = file_get_contents(self::FILE_PATH);

        return json_decode($oldData, true);
    }
}
