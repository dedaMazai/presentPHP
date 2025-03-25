<?php

namespace App\Http\Resources\V3\Sales\Contract;

use App\Models\V3\Sales\Customer\Customer;
use Carbon\Carbon;
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
        /** @var Customer $this */
        return [
            'id' => $this->getId(),
            'joint_owner_id' => $this->getJointOwnerId(),
            'first_name' => $this->getFirstName(),
            'last_name' => $this->getLastName(),
            'middle_name' => $this->getMiddleName(),
            'account_id' => $this->getAccountId(),
            'account_type' => $this->getConfidant() ? 'legal' : 'individual',
            'name' => $this->getFirstName(),
            'inn' => $this->getInn(),
            'signatory' => $this->getConfidant() ? 'owner' : 'signatory',
            'label' => $this->getLabel()
        ];
    }
}
