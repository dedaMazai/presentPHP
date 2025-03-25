<?php

namespace App\Http\Resources;

use App\Http\Resources\Article\ArticleCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountAdditionalInfoResource extends JsonResource
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
            'name' => $this->getUkProject()->name,
            'service_seller_name' => $this->getServiceSeller()->getName(),
            'description' => $this->getUkProject()->description,
            'realty_type' => $this->getRealtyType()->value,
            'account_number' => $this->getNumber(),
            'address' => $this->getAddress(),
            'postcode' => $this->getUkProject()->postcode,
            'number' => $this->getAddressNumber(),
            'rooms' => $this->getRooms(),
            'total_area' => $this->getTotalArea(),
            'living_area' => $this->getLivingArea(),
            'meters_count' => $this->getMetersCount(),
            'floor' => $this->getFloor(),
            'image' => new ImageResource($this->getUkProject()->image),
            'articles' => new ArticleCollection($this->getUkProject()->articles)
        ];
    }
}
