<?php

namespace App\Services\V2\Sales\JointOwner\Dto;

use App\Models\Role;
use App\Models\Sales\OwnerType;

/**
 * Class CreateJointOwnerLeadDto
 *
 * @package App\Services\Sales\JointOwner\Dto
 */
class CreateJointOwnerLeadDto
{
    public function __construct(
        public OwnerType $ownerType,
        public string $lastName,
        public string $firstName,
        public ?string $middleName,
        public ?string $gender,
        public ?string $phone,
        public ?string $email,
        public string $birthDate,
        public ?string $inn,
        public ?string $snils,
        public ?Role $role,
        public ?bool $married,
        public string $isRus,
        public string $part,
        public ?array $files,
    ) {
    }
}
