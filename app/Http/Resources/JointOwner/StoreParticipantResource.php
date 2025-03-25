<?php

namespace App\Http\Resources\JointOwner;

use App\Http\Resources\Meter\MeterEnterPeriodResource;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreParticipantResource extends JsonResource
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
            'id' => '',
            'type_message' => '',
            'message' => '',
        ];
    }
}
