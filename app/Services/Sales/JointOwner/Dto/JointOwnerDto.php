<?php

namespace App\Services\Sales\JointOwner\Dto;

use App\Models\Role;
use App\Models\Sales\OwnerType;

/**
 * Class JointOwnerDto
 *
 * @package App\Services\Sales\JointOwner\Dto
 */
class JointOwnerDto
{
    public function __construct(
        public string $id,
        public string $jointOwnerId,
        public string $fullName,
        public string $part,
        public ?OwnerType $type,
        public string $ageCategory,
        public bool $isAllProfileData,
        public ?string $label,
    ) {
    }
}
