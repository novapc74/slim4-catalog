<?php

namespace App\Enum\SQL\Product;

enum ProductSql: string
{
    case PRODUCT_BREADCRUMBS = "
    WITH RECURSIVE category_tree AS (
    SELECT id, title, slug, parent_category_id
    FROM categories
    WHERE id = :categoryId
    
    UNION ALL
    
    SELECT c.id, c.title, c.slug, c.parent_category_id
    FROM categories c
    INNER JOIN category_tree ct ON ct.parent_category_id = c.id
)

    SELECT ct.title, ct.slug, COUNT(DISTINCT CASE WHEN cit.slug = :citySlug THEN p.id END) AS product_count
    FROM category_tree ct
    LEFT JOIN products p ON p.category_id = ct.id
    LEFT JOIN prices pr ON pr.product_id = p.id
    LEFT JOIN cities cit ON pr.city_id = cit.id
    GROUP BY ct.title, ct.slug;";
    case PRODUCT_BY_SLUG = "SELECT
    p.id,
    p.title,
    p.slug,
    br.title as brand,
    pd.shop_code,
    pd.sku,
    pd.description,
    JSON_ARRAYAGG(
        DISTINCT JSON_OBJECT(
            'title', pr.title,
            'value', prp.value,
            'measure', pr.measure
        )
        ORDER BY pr.title, prp.value
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
LEFT JOIN product_properties prp ON p.id = prp.product_id AND prp.value IS NOT NULL
INNER JOIN properties pr ON pr.id = prp.property_id AND pr.is_invisible = FALSE
LEFT JOIN prices pri ON pri.product_id = p.id 
INNER JOIN cities ci ON pri.city_id = ci.id
INNER JOIN price_types pt ON pri.price_type_id = pt.id
INNER JOIN product_identifiers pd ON p.product_identifier_id = pd.id
WHERE p.slug = :productSlug AND ci.slug = :citySlug
GROUP BY p.id, br.title;";
}
