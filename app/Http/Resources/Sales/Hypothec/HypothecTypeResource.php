<?php

namespace App\Http\Resources\Sales\Hypothec;

use Illuminate\Http\Resources\Json\JsonResource;

class HypothecTypeResource extends JsonResource
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
            'code' => $this['Code'],
            'name' => $this['Name'],
        ];
    }
}
