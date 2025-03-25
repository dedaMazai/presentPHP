<?php

namespace App\Http\Resources\Sales\JointOwner;

use Illuminate\Http\Resources\Json\JsonResource;

class ContractJointOwnerResource extends JsonResource
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
            'name' => $this['joint_owner_id'],
            'gk_id' => $this['lastName'] . $this['firstName'] . $this['middleName']??'',
        ];
    }
}
