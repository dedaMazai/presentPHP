<?php

namespace App\Services\RelationshipInvite\Dto;

use App\Models\Role;
use Carbon\Carbon;

/**
 * Class CreateJointOwnerDto
 *
 * @package App\Services\RelationshipInvite\Dto
 */
class CreateContractJointOwnerDto
{
    /**
     * @param string      $customerId
     * @param string      $lastName
     * @param string      $firstName
     * @param string|null $middleName
     * @param string      $phone
     * @param Carbon      $birthDate
     * @param string      $email
     * @param Role        $role
     */
    public function __construct(
        public string $customerId,
        public string $lastName,
        public string $firstName,
        public ?string $middleName,
        public string $phone,
        public Carbon $birthDate,
        public string $email,
        public Role $role,
    ) {
    }
}
