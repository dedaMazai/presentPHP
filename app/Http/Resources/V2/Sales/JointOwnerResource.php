<?php

namespace App\Http\Resources\V2\Sales;

use App\Models\V2\Sales\Customer\Customer;
use Carbon\Carbon;
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
        /** @var Customer $this */
        $age = Carbon::parse($this->getBirthDate())->age;
        $ageCategory = '';

        if ($age > 18) {
            $ageCategory = 'adult';
        } elseif ($age > 14 && $age < 18) {
            $ageCategory = 'teen';
        } elseif ($age < 14) {
            $ageCategory = 'child';
        }

        return [
            'id' => $this->getId(),
            'joint_owner_id' => $this->getJointOwnerId(),
            'first_name' => $this->getFirstName(),
            'last_name' => $this->getLastName(),
            'middle_name' => $this->getMiddleName(),
            'is_depositor' => $this->getIsDepositor(),
            'part' => $this->getPart(),
            'is_customer' => $this->getIsCustomer(),
            'type' => $this->getOwnerType()?[
                'code' => $this->getOwnerType()->value,
                'name' => $this->getOwnerType()->label
            ]:null,
            'age_category' => $ageCategory,
            'label' => $this->getLabel(),
        ];
    }
}
