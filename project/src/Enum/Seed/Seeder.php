<?php

namespace App\Enum\Seed;

enum Seeder: string
{
    case SEED_CITY = "INSERT INTO cities (title, slug) VALUES
    ('Санкт-Петербург', 'spb'),
    ('Москва', 'msk'),
    ('Ростов-на-Дону', 'rnd')";
}
