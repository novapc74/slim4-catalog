<?php

namespace App\Enum\Seed;

enum Seeder: string
{
    case SEED_CITY = "INSERT INTO cities (title, slug) VALUES
    ('Санкт-Петербург', 'spb'),
    ('Москва', 'msk'),
    ('Ростов-на-Дону', 'rnd')";

    case SEED_PRICE_TYPE = "INSERT INTO cities (title, slug) VALUES
    ('розничная', 'retail'),
    ('акционная', 'action'),
    ('по карте', 'promotion'),
    ('оптовая', 'opt'),
    ('стоп', 'stop')";
}
