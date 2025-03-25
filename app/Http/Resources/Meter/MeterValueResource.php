<?php

namespace App\Http\Resources\Meter;

use Illuminate\Http\Resources\Json\JsonResource;

class MeterValueResource extends JsonResource
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
            'tariff_id' => $this->getTariffId(),
            'tariff_name' => $this->getTariffName(),
            'current_value' => round($this->getCurrentValue(), 3),
            'previous_value' => round($this->getPreviousValue(), 3),
        ];
    }
}
