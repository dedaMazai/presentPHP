<?php

namespace App\Http\Resources\Meter;

use Illuminate\Http\Resources\Json\JsonResource;

class MeterTariffValueResource extends JsonResource
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
            'subtype' => $this->getSubtype(),
            'cost' => $this->getCost(),
        ];
    }
}
