<?php

namespace App\Service\Command\SyncData;

use App\Service\Command\Interface\SyncDatabaseInterface;
use Generator;

final class SyncEntityService
{
    public static function update(SyncDatabaseInterface $instance): ?int
    {
        $i = 0;
        $fileName = $instance::getFileName();
        if (!file_exists($fileName)) {
            return null;
        }

        $classFqcn = $instance::getEntityFqcn();
        $method = 'upsert' . class_basename($classFqcn);
        $collection = [];

        #TODO сделать обновление чанками, чтобы коллекция не превышала максимально допустимое для базы данных...
        foreach (self::getCollection($fileName) as $item) {
            if (!$resolvedItems = $instance::getEntityItem($item)) {
                continue;
            }

            if (count($resolvedItems) > 1) {
                $instance::getEntityFqcn()::$method($resolvedItems);
                $i += count($resolvedItems);
                continue;
            }

            $collection[] = $resolvedItems[0];
        }

        if (count($collection)) {
            $i += count($collection);
            $instance::getEntityFqcn()::$method($collection);
        }

        return $i;
    }

    private static function getCollection(string $fileName): Generator
    {
        foreach (self::getDataFromFile($fileName) ?? [] as $item) {
            yield $item;
        }
    }

    private static function getDataFromFile(string $fileName): ?array
    {
        if (!file_exists($fileName)) {
            return null;
        }

        $oldData = file_get_contents($fileName);

        return json_decode($oldData, true);
    }
}
