<?php

namespace App\Services\User\Dto;

use App\Models\Role;

/**
 * Class SyncAccountDto
 *
 * @package App\Services\User\Dto
 */
class SyncAccountDto
{
    public function __construct(
        public string $accountNumber,
        public Role $role,
    ) {
    }
}
