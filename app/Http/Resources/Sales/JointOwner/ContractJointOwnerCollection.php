<?php

namespace App\Http\Resources\Sales\JointOwner;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ContractJointOwnerCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
