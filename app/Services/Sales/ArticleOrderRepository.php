<?php

namespace App\Services\Sales;

use App\Models\Sales\ArticleOrder;

/**
 * Class ArticleOrderRepository
 *
 * @package App\Services\Sales
 */
class ArticleOrderRepository
{
    public function makeArticleOrder(array $data): ArticleOrder
    {
        return new ArticleOrder(
            id: $data['id'],
            name: $data['name'],
            additionalOptionId: $data['AdditionalOptionId'] ?? null,
            quantity: $data['quantity'] ?? null,
            unitCode: $data['unitCode']['code'] ?? null,
            unitName: $data['unitCode']['name'] ?? null,
            cost: $data['cost'] ?? null,
            price: $data['price'] ?? null,
            sum: $data['sum'] ?? null,
            serviceId: $data['serviceId'] ?? null,
            propertyId: $data['articleId'] ?? null,
        );
    }
}
