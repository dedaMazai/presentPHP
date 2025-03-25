<?php

namespace App\Http\Resources\Sales;

use Illuminate\Http\Resources\Json\JsonResource;

class CharacteristicSaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'type' => $this->getType()->value,
            'group' => $this->getGroup(),
            'parameter' => $this->getParameter(),
            'url' => $this->getUrl(),
            'order' => $this->getOrder(),
            'choice_type' => $this->getChoiceType()?->value,
            'is_selected' => $this->getIsSelected(),
            'discount_type' => $this->getDiscountType()?->value,
            'discount_sum' => $this->getDiscountSum(),
            'discount_percent' => $this->getDiscountPercent(),
            'sum' => $this->getSum(),
        ];
    }
}
