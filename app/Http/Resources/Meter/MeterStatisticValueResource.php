<?php

namespace App\Http\Resources\Meter;

use Illuminate\Http\Resources\Json\JsonResource;

class MeterStatisticValueResource extends JsonResource
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
            'tariff' => $this->getTariff(),
            'total' => round($this->getTotal(), 2),
            'average' => round($this->getAverage(), 2),
            'data' => $this->getData(),
        ];
    }
}
