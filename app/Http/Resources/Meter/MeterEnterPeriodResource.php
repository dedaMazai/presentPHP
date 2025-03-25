<?php

namespace App\Http\Resources\Meter;

use Illuminate\Http\Resources\Json\JsonResource;

class MeterEnterPeriodResource extends JsonResource
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
            'start_date' => $this->getStartDate()->toDateString(),
            'end_date' => $this->getEndDate()->toDateString()
        ];
    }
}
