<?php

namespace App\Services\V2\Sales;

use App\Models\V2\Sales\ArticleOrder;

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
            code: $data['serviceCode'] ?? null,
            articleStatusReceptionCode: $data['articleStatusReception']['code'] ?? null,
        );
    }
}
