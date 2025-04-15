<?php

namespace App\Service\Command\Interface;

interface GenerateSlugInterface
{
    public static function generateSlug(string $title): string;

    public static function addResolvedTitle(string $slug): void;

    public static function getResolvedTitles(): array;
}
