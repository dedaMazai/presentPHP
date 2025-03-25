<?php

namespace App\Http\Resources\Sales;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
            'gk_id' => $this->getGkId(),
            'address' => $this->getAddress(),
            'region_code' => intval($this->getRegionCode()),
            'region_name' => $this->getRegionName(),
            'website' => $this->getWebsite(),
        ];
    }
}
