<?php

namespace App\Enum\Database;

/**
 * Важен порядок создания таблиц.
 * Первая таблица сверху, последняя снизу.
 * По хорошему - можно сделать все через миграции eloquent, но не хочется "утяжелять" фреймворк.
 * Возможно, реализую создание/удаление таблиц через консольную команду.
 */
enum CreateTable: string
{
    case CREATE_CITY = "CREATE TABLE cities (
    id SMALLINT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE)";

    case CREATE_BRAND = "CREATE TABLE brands (
    id SMALLINT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE)";
    case CREATE_MEASURE = "CREATE TABLE measures (
    id SMALLINT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(50) NOT NULL UNIQUE)";
    case CREATE_PRICE_TYPE = "CREATE TABLE price_types(
    id    SMALLINT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(50) NOT NULL UNIQUE,
    slug  VARCHAR(50) NOT NULL UNIQUE)";
    case CREATE_PROPERTY = "CREATE TABLE properties (
    id SMALLINT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(50) NOT NULL UNIQUE,
    is_invisible TINYINT(1) NOT NULL DEFAULT 0,
    measure_id SMALLINT,
    FOREIGN KEY (measure_id) REFERENCES measures(id))";
    case CREATE_CATEGORY = "CREATE TABLE categories (
    id UUID PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    parent_category_id UUID,
    FOREIGN KEY (parent_category_id) REFERENCES categories(id))";
    case CREATE_PRODUCT = "CREATE TABLE products (
    id UUID PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    brand_id SMALLINT,
    FOREIGN KEY (brand_id) REFERENCES brands(id),
    category_id UUID,
    FOREIGN KEY (category_id) REFERENCES categories(id))";
    case CREATE_PRICE = "CREATE TABLE prices (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    value SMALLINT NOT NULL,
    product_id UUID,
    FOREIGN KEY (product_id) REFERENCES products(id),
    city_id SMALLINT,
    FOREIGN KEY (city_id) REFERENCES cities(id),
    price_type_id SMALLINT,
    FOREIGN KEY (price_type_id) REFERENCES price_types(id))";
}
