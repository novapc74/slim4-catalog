<?php

namespace App\Enum\DataBase;

enum CreateTable: string
{
    case CREATE_CITY = "CREATE TABLE cities (
    id SMALLINT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE
)";
    case CREATE_CATEGORY = "CREATE TABLE categories (
    id UUID PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    parent_category_id UUID,
    FOREIGN KEY (parent_category_id) REFERENCES categories(id)
)";

}
