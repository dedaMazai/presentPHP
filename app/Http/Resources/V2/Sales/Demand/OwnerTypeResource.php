<?php

namespace App\Http\Resources\V2\Sales\Demand;

use App\Models\Sales\Ownership;
use Illuminate\Http\Resources\Json\JsonResource;

class OwnerTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var Ownership $this */
        return [
            'code' => $this->getCode(),
            'name' => $this->getName(),
        ];
    }
}
