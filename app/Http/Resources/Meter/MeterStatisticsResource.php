<?php

namespace App\Http\Resources\Meter;

use Illuminate\Http\Resources\Json\JsonResource;

class MeterStatisticsResource extends JsonResource
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
            'type' => $this->getType()->value,
            'subtype' => $this->getSubtype()?->value,
            'unit' => $this->getUnit(),
            'total' => round($this->getTotal(), 2),
            'average' => round($this->getAverage(), 2),
            'statistics' => !empty($this->getStatistics()) ? new MeterStatisticValueCollection($this->getStatistics()) : null
        ];
    }
}
