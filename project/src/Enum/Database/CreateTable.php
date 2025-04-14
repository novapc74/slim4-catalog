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
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE);";

    case CREATE_BRAND = "CREATE TABLE brands (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE);";
    case CREATE_PRICE_TYPE = "CREATE TABLE price_types(
    id    INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(50) NOT NULL UNIQUE,
    slug  VARCHAR(50) NOT NULL UNIQUE);";
    case CREATE_PROPERTY = "CREATE TABLE properties (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL UNIQUE,
    measure VARCHAR(255),
    is_invisible TINYINT(1) NOT NULL DEFAULT 0);";
    case CREATE_CATEGORY = "CREATE TABLE categories (
    id UUID PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    parent_category_id UUID,
    FOREIGN KEY (parent_category_id) REFERENCES categories(id));";
    case CREATE_PRODUCT_IDENTIFIER = "CREATE TABLE product_identifiers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    shop_code VARCHAR(25) UNIQUE,
    sku VARCHAR(50),
    description TEXT);";
    case CREATE_PRODUCT = "CREATE TABLE products (
    id UUID PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    brand_id INT,
    category_id UUID,
    product_identifier_id INT,
    FOREIGN KEY (brand_id) REFERENCES brands(id),
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (product_identifier_id) REFERENCES product_identifiers(id));";
    case CREATE_PRICE = "CREATE TABLE prices (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    value INT NOT NULL,
    product_id UUID,
    FOREIGN KEY (product_id) REFERENCES products(id),
    city_id INT,
    FOREIGN KEY (city_id) REFERENCES cities(id),
    price_type_id INT,
    FOREIGN KEY (price_type_id) REFERENCES price_types(id));";
    case CREATE_PRODUCT_PROPERTY = "CREATE TABLE product_properties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    value VARCHAR(255),
    product_id UUID,
    FOREIGN KEY (product_id) REFERENCES products(id),
    property_id INT,
    FOREIGN KEY (property_id) REFERENCES properties(id));";
}
