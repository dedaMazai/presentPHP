<?php

namespace App\Http\Resources\Claim;

use Illuminate\Http\Resources\Json\JsonResource;

class ClaimPassHomanResource extends JsonResource
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
            'full_name' => $this->getFullName(),
        ];
    }
}
