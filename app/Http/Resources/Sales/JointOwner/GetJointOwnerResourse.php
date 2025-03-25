<?php

namespace App\Http\Resources\Sales\JointOwner;

use App\Http\Resources\JointOwnerDocumentCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class GetJointOwnerResourse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $documents = [];

        foreach ($this->documents as $document) {
            $documents[] = [
                'document' => $document,
                'jointOwnerId' => $this->id
            ];
        }

        return [
            'id' => $this->id,
            'joint_owner_id' => $this->jointOwnerId,
            'last_name' => $this->lastName,
            'first_name' => $this->firstName,
            'middle_name' => $this->middleName,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'email' => $this->email,
            'age_category' => $this->ageCategory,
            'birth_date' => $this->birthDate,
            'documents' => new JointOwnerDocumentCollection($documents),
            'married' => $this->married,
            'inn' => $this->inn,
            'snils' => $this->snils,
            'part' => $this->part,
            'owner_type' => $this->ownerCode,
            'role_code' => $this->roleCode,
            'is_rus' => $this->isRus,
            'is_customer' => $this->isCustomer,
            'is_depositor' => $this->isDepositor,
        ];
    }
}
