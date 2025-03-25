<?php

namespace App\Http\Resources\Claim;

use Illuminate\Http\Resources\Json\JsonResource;

class SimpleClaimServiceResource extends JsonResource
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
            'category_name' => $this->getCatalogueItemParentName(),
            'catalogue_item' => new SimpleClaimCatalogueItemResource($this->getCatalogueItem())
        ];
    }
}
