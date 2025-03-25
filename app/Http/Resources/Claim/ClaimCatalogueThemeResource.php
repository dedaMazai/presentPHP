<?php

namespace App\Http\Resources\Claim;

use Illuminate\Http\Resources\Json\JsonResource;

class ClaimCatalogueThemeResource extends JsonResource
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
            'items' => new ClaimCatalogueItemCollection($this->getItems())
        ];
    }
}
