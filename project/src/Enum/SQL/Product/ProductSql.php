<?php

namespace App\Enum\SQL\Product;

enum ProductSql: string
{
    case PRODUCT_BY_SLUG = "SELECT
    p.id,
    p.title,
    p.slug,
    br.title as brand,
    JSON_ARRAYAGG(
        DISTINCT JSON_OBJECT(
            'title', pr.title,
            'value', prp.value,
            'measure', pr.measure
        )
        ORDER BY pr.title
    ) AS properties,
    JSON_ARRAYAGG(
        DISTINCT JSON_OBJECT(
            'value', pri.value,
            'price_type', pt.title
        )
        ORDER BY pri.value DESC
    ) AS prices
FROM products p
LEFT JOIN brands br ON p.brand_id = br.id
LEFT JOIN product_properties prp ON p.id = prp.product_id
INNER JOIN properties pr ON pr.id = prp.property_id
INNER JOIN prices pri ON pri.product_id = p.id 
INNER JOIN cities ci ON pri.city_id = ci.id
INNER JOIN price_types pt ON pri.price_type_id = pt.id
WHERE p.slug = :productSlug AND ci.slug = :citySlug
AND prp.value IS NOT NULL  -- Исключаем записи с NULL значением
GROUP BY p.id, br.title;";
}
