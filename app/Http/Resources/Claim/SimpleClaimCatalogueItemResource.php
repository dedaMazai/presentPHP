<?php

namespace App\Http\Resources\Claim;

use Illuminate\Http\Resources\Json\JsonResource;

class SimpleClaimCatalogueItemResource extends JsonResource
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
            'group' => $this->getGroup()->value,
            'is_popular' => $this->getIsPopular(),
            'is_service' => $this->getIsService(),
            'title' => $this->getTitle(),
            'price_type' => $this->getPriceType()?->value,
            'images' => new SimpleClaimCatalogueItemImagesResource($this->getImages()),
            'children' => new SimpleClaimCatalogueItemCollection($this->getChildren())
        ];
    }
}
