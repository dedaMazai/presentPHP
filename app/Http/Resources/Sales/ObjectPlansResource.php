<?php

namespace App\Http\Resources\Sales;

use App\Models\Sales\Property\ObjectPlan;
use Illuminate\Http\Resources\Json\JsonResource;

class ObjectPlansResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var ObjectPlan $this */
        return [
            'common' => $this->getCommon(),
            'object' => $this->getObject(),
            'floor' => $this->getFloor(),
        ];
    }
}
