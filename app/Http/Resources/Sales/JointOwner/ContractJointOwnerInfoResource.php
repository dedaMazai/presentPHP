<?php

namespace App\Http\Resources\Sales\JointOwner;

use App\Models\Sales\Customer\Customer;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractJointOwnerInfoResource extends JsonResource
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
        $age = $this->getBirthDate()?->age;
        $ageCategory = null;

        if ($age > 18) {
            $ageCategory = 'adult';
        } elseif ($age > 14 && $age < 18) {
            $ageCategory = 'teen';
        } elseif ($age < 14) {
            $ageCategory = 'child';
        }

        $documentInfo = null;
        if ($this->getDocumentSeries()  != null && $this->getDocumentNumber() != null) {
            $documentInfo = $this->getDocumentSeries() . ' ' . $this->getDocumentNumber();
        }

        return [
            'id' => $this->getId(),
            'joint_owner_id' => $this->getJointOwnerId(),
            'full_name' => $this->getLastName() . ' ' . $this->getFirstName() . ' ' . $this->getMiddleName()??'',
            'age_category' => $ageCategory,
            'birth_date' => $this->getBirthDate()?->format('d.m.Y'),
            'birth_place' => $this->getBirthPlace(),
            'address_registration' => $this->getAddressRegistration(),
            'document_info' => $documentInfo,
            'document_give_out' => $this->getDocumentGiveOut(),
            'document_date' => $this->getDocumentDate()?->format('d.m.Y'),
            'subdivision_code' => $this->getSubdivisionCode(),
            'snils' => $this->getSnils(),
            'inn' => $this->getInn(),
            'married' => $this->getMarried(),
        ];
    }
}
