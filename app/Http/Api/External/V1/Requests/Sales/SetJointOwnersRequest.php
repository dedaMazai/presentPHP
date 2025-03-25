<?php

namespace App\Http\Api\External\V1\Requests\Sales;

use App\Http\Api\External\V1\Requests\Request;
use App\Models\Document\DocumentSubtype;
use App\Models\Document\DocumentType;
use App\Models\Sales\FamilyStatus;
use App\Models\Sales\OwnerObjectType;
use App\Models\Sales\OwnerType;
use Illuminate\Validation\Rule;

/**
 * Class SetJointOwnersRequest
 *
 * @package App\Http\Api\External\V1\Requests\Sales
 */
class SetJointOwnersRequest extends Request
{
    public function rules(): array
    {
        $rules = [
            'owner_type' => [
                'required',
                Rule::in(OwnerType::toValues()),
            ],
            'owners' => 'array',
            'owners.*.owner_object_type' => [
                'required',
                Rule::in(OwnerObjectType::toValues()),
            ],
            'owners.*.middle_name' => 'string',
            'owners.*.inn' => 'required|string',
            'owners.*.' . DocumentType::snils()->value . '.0.0' => 'required|file',
        ];

        $ownerType = OwnerType::from($this->owner_type);
        if ($ownerType->equals(OwnerType::personal())) {
            $rules = array_merge($rules, $this->getPersonal());
        } elseif ($ownerType->equals(OwnerType::joint())) {
            $rules = array_merge($rules, $this->getJoint());
        } else {
            $rules = array_merge($rules, $this->getShared());
        }

        return $rules;
    }

    private function getPersonal(): array
    {
        return [
            'owners.*.last_name' => $this->getRuleForNotMySelf(),
            'owners.*.first_name' => $this->getRuleForNotMySelf(),
            'owners.*.birth_date' => $this->getRuleForNotMySelf(),
            'owners.*.email' => $this->getRuleForNotChildUnder14(),
            'owners.*.phone' => $this->getRuleForNotChildUnder14(),
            'owners.*.family_status' => [
                $this->getRuleForAdult(),
                Rule::in(FamilyStatus::toValues()),
            ],
            'owners.*.' . DocumentType::identification()->value . '.' . DocumentSubtype::passport()->value . '.0' => [
                'file',
                $this->getRuleForAdultPassport(),
            ],
            'owners.*.' . DocumentType::identification()->value . '.' . DocumentSubtype::passport()->value . '.1' => [
                'file',
                $this->getRuleForAdultPassport(),
            ],
            'owners.*.' . DocumentType::identification()->value . '.' . DocumentSubtype::birthCertificate()->value . '.0' => [
                'file',
                $this->getRuleForChildBirthCertificate(),
            ],
        ];
    }

    private function getJoint(): array
    {
        return [
            'owners.*.last_name' => $this->getRuleForNotMySelf(),
            'owners.*.first_name' => $this->getRuleForNotMySelf(),
            'owners.*.birth_date' => $this->getRuleForNotMySelf(),
            'owners.*.email' => 'required|string',
            'owners.*.phone' => $this->getRuleForNotMySelf(),
            'owners.*.' . DocumentType::identification()->value . '.' . DocumentSubtype::passport()->value . '.0' => 'required|file',
            'owners.*.' . DocumentType::identification()->value . '.' . DocumentSubtype::passport()->value . '.1' => 'required|file',
        ];
    }

    private function getShared(): array
    {
        return [
            'owners.*.last_name' => $this->getRuleForNotMySelf(),
            'owners.*.first_name' => $this->getRuleForNotMySelf(),
            'owners.*.birth_date' => $this->getRuleForNotMySelf(),
            'owners.*.email' => $this->getRuleForNotChildUnder14(),
            'owners.*.phone' => $this->getRuleForNotChildUnder14(),
            'owners.*.family_status' => [
                $this->getRuleForAdult(),
                Rule::in(FamilyStatus::toValues()),
            ],
            'owners.*.part' => 'required|string',
            'owners.*.' . DocumentType::identification()->value . '.' . DocumentSubtype::passport()->value . '.0' => [
                'file',
                $this->getRuleForAdultPassport(),
            ],
            'owners.*.' . DocumentType::identification()->value . '.' . DocumentSubtype::passport()->value . '.1' => [
                'file',
                $this->getRuleForAdultPassport(),
            ],
            'owners.*.' . DocumentType::identification()->value . '.' . DocumentSubtype::birthCertificate()->value . '.0' => [
                'file',
                $this->getRuleForChildBirthCertificate(),
            ],
        ];
    }

    private function getRuleForNotMySelf(): string
    {
        return 'required_unless:owners.*.owner_object_type,' . OwnerObjectType::myself()->value . '|string';
    }

    private function getRuleForAdult(): string
    {
        $adultTypes = [
            OwnerObjectType::myself()->value,
            OwnerObjectType::person()->value,
            OwnerObjectType::childPresenter()->value,
        ];

        return 'required_if:owners.*.owner_object_type,' . implode(',', $adultTypes);
    }

    private function getRuleForNotChildUnder14(): string
    {
        return 'required_unless:owners.*.owner_object_type,' .
            OwnerObjectType::childUnder14()->value . ',' . OwnerObjectType::myself()->value . '|string';
    }

    private function getRuleForAdultPassport(): string
    {
        $adultPassportTypes = [
            OwnerObjectType::myself()->value,
            OwnerObjectType::person()->value,
            OwnerObjectType::childPresenter()->value,
            OwnerObjectType::childOlder14()->value,
        ];

        return 'required_if:owners.*.owner_object_type,' . implode(',', $adultPassportTypes);
    }

    private function getRuleForChildBirthCertificate(): string
    {
        $childBirthTypes = [
            OwnerObjectType::childUnder14()->value,
            OwnerObjectType::childOlder14()->value,
        ];

        return 'required_if:owners.*.owner_object_type,' . implode(',', $childBirthTypes);
    }
}
