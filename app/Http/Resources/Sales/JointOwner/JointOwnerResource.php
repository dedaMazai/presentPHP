<?php

namespace App\Http\Resources\Sales\JointOwner;

use Illuminate\Http\Resources\Json\JsonResource;

class JointOwnerResource extends JsonResource
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
            'contact_id' => $this['contactId'],
            'joint_owner_id' => $this['jointOwnerId'],
            'type_message' => 1,
            'message' => '',
        ];
    }
}
