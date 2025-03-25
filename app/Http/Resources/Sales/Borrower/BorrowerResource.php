<?php

namespace App\Http\Resources\Sales\Borrower;

use Illuminate\Http\Resources\Json\JsonResource;

class BorrowerResource extends JsonResource
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
            'id' => $this['id'],
            'joint_owner_id' => $this['jointOwnerId'],
            'full_name' => $this['fullName'],
        ];
    }
}
