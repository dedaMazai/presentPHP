<?php

namespace App\Http\Resources\Sales\Hypothec;

use Illuminate\Http\Resources\Json\JsonResource;

class HypothecResource extends JsonResource
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
            'demaid_id' => $this['hypothec']['demandMainId'],
            'id' => $this['hypothec']['id'],
            'manager_hyphotec' => new ManagerHypothecResource($this['ownerObject']),
        ];
    }
}
