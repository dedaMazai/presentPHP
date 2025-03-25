<?php

namespace App\Http\Resources\Contract\JointOwner;

use App\Models\SignDocuments;
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
            'сourier_sign_info' => $this->courierSignInfo,
            'is_common_info_signme_available' => $this->isCommonInfoSignMeAvailable,
            'reissue_guide_url' => $this->reissueGuideUrl,
            'joint_owners' => new JointOwnerListCollection($this->jointOwners),
        ];
    }
}
