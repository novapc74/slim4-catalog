<?php

namespace App\Enum;

enum SQL: string
{
    case MAIN_CATEGORY = "SELECT c.slug, c.title, COUNT(ch.id) AS child_category_count FROM category AS c
                LEFT JOIN category AS ch ON c.id = ch.parent_category_id
                WHERE c.parent_category_id IS NULL
                GROUP BY c.title 
                ORDER BY title LIMIT 20";

    case CATEGORY_PRODUCTS = "
        SELECT
    product.title,
    product.slug,
    COALESCE(CEILING(leftovers.quantity), 0) AS leftovers,
    (SELECT 
         CAST(SUBSTRING_INDEX(product_property.value, ':', 1) AS DECIMAL(10, 2)) /
            CAST(REPLACE(SUBSTRING_INDEX(product_property.value, ':', -1), ',', '.') AS DECIMAL(10, 2))
        FROM product_property
        INNER JOIN property ON product_property.property_id = property.id
        WHERE product_property.product_id = product.id
          AND property.title = :square_ratio
        LIMIT 1
    ) AS square_ratio,
    
    (SELECT product_property.value
        FROM product_property
        INNER JOIN property ON product_property.property_id = property.id
        WHERE product_property.product_id = product.id
          AND property.title = :unit
        LIMIT 1
    ) AS unit,

    (SELECT product_property.value
        FROM product_property
        INNER JOIN property ON product_property.property_id = property.id
        WHERE product_property.product_id = product.id
          AND property.title = :shop_code
        LIMIT 1
    ) AS shop_code,

    JSON_OBJECTAGG(price_type.type, price.value) AS prices,
    
    JSON_ARRAYAGG(
        DISTINCT JSON_OBJECT(
            'title', property.title,
            'value', product_property.value,
            'measure', COALESCE(measure.title, NULL)
        )
        ORDER BY
            CASE
                WHEN property.title NOT IN (:blacklist) THEN 0
                ELSE 1
            END
    ) AS properties,

    (SELECT JSON_ARRAYAGG(
        JSON_OBJECT(
            'text', label.text,
            'text_color', label.text_color,
            'background_color', label.background_color
        )
    )
    FROM product_label_connection
    INNER JOIN label ON product_label_connection.label_id = label.id
    INNER JOIN label_city_connection ON label.id = label_city_connection.label_id
    INNER JOIN city ON label_city_connection.city_id = city.id
    WHERE product_label_connection.product_id = product.id
      AND city.slug = :citySlug
    ) AS labels

FROM product
INNER JOIN category ON product.category_id = category.id
LEFT JOIN price ON product.id = price.product_id
LEFT JOIN price_type ON price.price_type_id = price_type.id
INNER JOIN city ON price.city_id = city.id
LEFT JOIN product_property ON product.id = product_property.product_id
LEFT JOIN property ON product_property.property_id = property.id
LEFT JOIN measure ON property.measure_id = measure.id
LEFT JOIN leftovers ON product.id = leftovers.product_id
WHERE category.slug = :categorySlug AND city.slug = :citySlug
  AND property.title NOT IN (:blacklist)
  AND product_property.value IS NOT NULL
GROUP BY product.id
ORDER BY (
    SELECT MIN(price.value)
    FROM price
    WHERE price.product_id = product.id AND price.city_id = city.id
)
LIMIT :limit OFFSET :offset";

    case CATEGORY_DATA = "
        WITH RECURSIVE cte AS (
            SELECT id, title, slug, parent_category_id, 1 AS level
            FROM category 
            WHERE slug = :categorySlug
            UNION ALL
            SELECT cat.id, cat.title, cat.slug, cat.parent_category_id, cte.level + 1
            FROM category cat
                     JOIN cte ON cat.id = cte.parent_category_id
        )
        SELECT
            cte.title,
            cte.slug,
            cte.level,
            (SELECT COUNT(*)
            FROM product
            LEFT JOIN price ON product.id = price.product_id
            INNER JOIN city ON price.city_id = city.id
            WHERE product.category_id = cte.id AND city.slug = :citySlug AND price.city_id = city.id) AS product_count,
            CASE
                WHEN level = 1 THEN
                    (SELECT JSON_ARRAYAGG(JSON_OBJECT('title', child.title, 'slug', child.slug))
                     FROM category child
                     WHERE child.parent_category_id = cte.id)
                END AS children
        FROM cte
        ORDER BY level";

    case FILTER_BRAND = "SELECT (*) FROM brand LIMIT 10";
    case FILTER_PROPERTY = "SELECT (*) FROM property LIMIT 10";

}
