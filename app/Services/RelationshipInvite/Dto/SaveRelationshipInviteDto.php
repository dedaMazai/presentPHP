<?php

namespace App\Services\RelationshipInvite\Dto;

use App\Models\Account\Account;
use App\Models\Role;
use App\Models\User\User;
use Carbon\Carbon;

/**
 * Class SaveRelationshipInviteDto
 *
 * @package App\Services\RelationshipInvite\Dto
 */
class SaveRelationshipInviteDto
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $phone,
        public Carbon $birthDate,
        public Role $role,
        public User $owner,
        public ?Account $account = null
    ) {
    }
}
