<?php

namespace App\Http\Resources\Sales\JointOwner;

use App\Services\Sales\JointOwner\Dto\JointOwnerDto;
use Illuminate\Http\Resources\Json\JsonResource;

class GetJointOwnersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var JointOwnerDto $this */
        return [
            'id' => $this->id,
            'joint_owner_id' => $this->jointOwnerId,
            'full_name' => $this->fullName,
            'part' => $this->part,
            'type' => $this->type,
            'age_category' => $this->ageCategory,
            'is_all_profile_data' => $this->isAllProfileData,
            'label' => $this->label
        ];
    }
}
