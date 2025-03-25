<?php

namespace App\Services\V2\Sales\Customer\Dto;

use App\Models\Role;
use App\Models\Sales\OwnerType;

/**
 * Class LegalEntityDto
 *
 * @package App\Services\V2\Sales\Customer\Dto
 */
class CombinedCustomerDto
{
    public function __construct(
        public object $legalEntityData,
        public object $individualOwner,
    ) {
    }
}
