<?php

namespace App\Services\Sales;

use App\Models\Sales\CharacteristicSale\CharacteristicSale;
use App\Models\Sales\CharacteristicSale\CharacteristicSaleType;
use App\Models\Sales\ChoiceType;
use App\Models\Sales\DiscountType;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;

/**
 * Class CharacteristicSaleRepository
 *
 * @package App\Services\Sales
 */
class CharacteristicSaleRepository
{
    public function __construct(private DynamicsCrmClient $dynamicsCrmClient,)
    {
    }

    /**
     * @return CharacteristicSale[]
     * @throws BadRequestException
     */
    public function getAllByPropertyId(string $propertyId, ?CharacteristicSaleType $type): array
    {
        $data = $this->dynamicsCrmClient->getCharacteristicSalesByPropertyId($propertyId);

        $characteristics = [];
        foreach ($data['characteristicSaleList'] as $characteristic) {
            if ($type && !$type->equals(CharacteristicSaleType::from($characteristic['processingType']['code']))) {
                continue;
            }

            $characteristics[] = $this->makeCharacteristicSale($characteristic);
        }

        return $characteristics;
    }

    public function makeCharacteristicSale(array $data): CharacteristicSale
    {
        // phpcs:disable
        return new CharacteristicSale(
            id: $data['id'],
            name: $data['name'],
            type: isset($data['processingType']['code']) ? CharacteristicSaleType::from($data['processingType']['code']) : null,
            group: $data['groupCharacteristics'] ?? null,
            parameter: $data['parameter'] ?? null,
            url: $data['url'] ?? null,
            order: $data['displayOrder'] ?? null,
            choiceType: ChoiceType::tryFrom($data['choiceTypeCode']['code'] ?? ''),
            isSelected: $data['choiceSet'] ?? null,
            discountType: DiscountType::tryFrom($data['discountTypeCode']['code'] ?? ''),
            discountSum: $data['discountSum'] ?? null,
            discountPercent: $data['discountPercent'] ?? null,
            sum: $data['sum'] ?? null,
        );
        // phpcs:enable
    }
}
