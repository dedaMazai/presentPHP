<?php

namespace App\Services\Sales\Demand\Dto;

use App\Models\Role;
use App\Models\Sales\FamilyStatus;
use App\Models\Sales\OwnerType;
use Carbon\Carbon;

/**
 * Class JointOwnerDto
 *
 * @package App\Services\Sales\Demand\Dto
 */
class JointOwnerDto
{
    /**
     * @param string|null             $jointOwnerId
     * @param string|null             $customerId
     * @param string|null             $lastName
     * @param string|null             $firstName
     * @param string|null             $middleName
     * @param string|null             $phone
     * @param Carbon|null             $birthDate
     * @param string|null             $email
     * @param string|null             $inn
     * @param string|null             $citizenship
     * @param FamilyStatus|null       $familyStatus
     * @param string|null             $part
     * @param OwnerType|null          $ownerType
     * @param Role|null               $role
     * @param JointOwnerDocumentDto[] $documents
     */
    public function __construct(
        public ?string $jointOwnerId,
        public ?string $customerId,
        public ?string $lastName,
        public ?string $firstName,
        public ?string $middleName,
        public ?string $phone,
        public ?Carbon $birthDate,
        public ?string $email,
        public ?string $inn,
        public ?string $citizenship,
        public ?FamilyStatus $familyStatus,
        public ?string $part,
        public ?OwnerType $ownerType,
        public ?Role $role,
        public array $documents,
    ) {
    }
}
