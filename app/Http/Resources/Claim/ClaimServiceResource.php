<?php

namespace App\Http\Resources\Claim;

use Illuminate\Http\Resources\Json\JsonResource;

class ClaimServiceResource extends JsonResource
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
            'amount' => $this->getAmount(),
            'cost' => $this->getCost(),
            'quantity' => $this->getQuantity(),
            'order_number' => $this->getOrderNumber(),
            'category_id' => $this->getCatalogueItemParentId(),
            'category_name' => $this->getCatalogueItemParentName(),
            'catalogue_item' => new ClaimCatalogueItemResource($this->getCatalogueItem())
        ];
    }
}
