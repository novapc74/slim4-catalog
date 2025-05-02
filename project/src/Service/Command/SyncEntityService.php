<?php

namespace App\Service\Command;

use App\Service\Command\Interface\SyncDatabaseInterface;
use Generator;

final class SyncEntityService
{
    private const CHUNK_SIZE = 1000;

    private static function getJsonChunk(string $fileName, int $chunkSize): Generator
    {
        if (!file_exists($fileName)) {
            yield [];
            return;
        }

        $jsonData = file_get_contents($fileName);
        $dataArray = json_decode($jsonData, true);

        if (!is_array($dataArray)) {
            yield [];
            return;
        }

        $chunk = [];
        foreach ($dataArray as $item) {
            $chunk[] = $item;

            if (count($chunk) >= $chunkSize) {
                yield $chunk;
                $chunk = [];
            }
        }

        if (count($chunk) > 0) {
            yield $chunk;
        }
    }

    public static function update(SyncDatabaseInterface $instance): int
    {
        $fileName = $instance::getFileName();
        $classFqcn = $instance::getEntityFqcn();
        $method = 'upsert' . class_basename($classFqcn);

        $i = 0;
        foreach (self::getJsonChunk($fileName, self::CHUNK_SIZE) as $chunks) {
            $collection = [];
            foreach ($chunks as $item) {
                if (!$resolvedItems = $instance::getEntityItem($item)) {
                    continue;
                }

                if (count($resolvedItems) > 1) {
                    $collection = array_merge($collection, $resolvedItems);
                    $i += count($resolvedItems);
                    continue;
                }

                $collection[] = $resolvedItems[0];
                $i++;
            }

            $instance::getEntityFqcn()::$method($collection);

        }

        return $i;
    }
}
