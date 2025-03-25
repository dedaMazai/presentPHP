<?php

namespace App\Models\Sales\CharacteristicSale;

use App\Models\Sales\ChoiceType;
use App\Models\Sales\DiscountType;

/**
 * Class CharacteristicSale
 *
 * @package App\Models\Sales
 */
class CharacteristicSale
{
    public function __construct(
        private string $id,
        private string $name,
        private ?CharacteristicSaleType $type,
        private ?string $group,
        private ?string $parameter,
        private ?string $url,
        private ?string $order,
        private ?ChoiceType $choiceType,
        private ?bool $isSelected,
        private ?DiscountType $discountType,
        private ?float $discountSum,
        private ?float $discountPercent,
        private ?float $sum,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): ?CharacteristicSaleType
    {
        return $this->type;
    }

    public function getGroup(): ?string
    {
        return $this->group;
    }

    public function getParameter(): ?string
    {
        return $this->parameter;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getOrder(): ?string
    {
        return $this->order;
    }

    public function getChoiceType(): ?ChoiceType
    {
        return $this->choiceType;
    }

    public function getIsSelected(): ?bool
    {
        return $this->isSelected;
    }

    public function getDiscountType(): ?DiscountType
    {
        return $this->discountType;
    }

    public function getDiscountSum(): ?float
    {
        return $this->discountSum;
    }

    public function getDiscountPercent(): ?float
    {
        return $this->discountPercent;
    }

    public function getSum(): ?float
    {
        return $this->sum;
    }
}
