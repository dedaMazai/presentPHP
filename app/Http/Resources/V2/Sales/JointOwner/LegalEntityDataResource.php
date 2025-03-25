<?php

namespace App\Http\Resources\V2\Sales\JointOwner;

use Illuminate\Http\Resources\Json\JsonResource;

class LegalEntityDataResource extends JsonResource
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
            'account_id'     => $this->getAccountId() ?? null,
            'joint_owner_id' => $this->getJointOwnerId() ?? null,
            'account_type'   => $this->getAccountType() ?? null,
            'name'           => $this->getName() ?? null,
            'inn'            => $this->getInn() ?? null,
        ];
    }
}
