<?php

namespace App\Service\Command\SyncData;

interface SyncDatabaseInterface
{
    public static function getEntityFqcn(): string;

    public static function getFileName(): ?string;

    public static function getEntityItem(array $data): ?array;
}
