<?php

namespace App\Services\V2\Sales\JointOwner\Dto;

use App\Models\Role;
use App\Models\Sales\OwnerType;

/**
 * Class GetJointOwnerDto
 *
 * @package App\Services\Sales\JointOwner\Dto
 */
class GetJointOwnerDto
{
    public function __construct(
        public string $id,
        public string $jointOwnerId,
        public string $lastName,
        public string $firstName,
        public ?string $middleName,
        public ?int $gender,
        public ?string $phone,
        public ?string $email,
        public string $ageCategory,
        public ?string $birthDate,
        public array $documents,
        public ?int $married,
        public ?string $inn,
        public ?string $snils,
        public ?string $part,
        public ?int $ownerCode,
        public ?int $roleCode,
        public bool $isRus,
        public bool $isCustomer,
        public bool $isDepositor,
        public ?string $label,
    ) {
    }
}
