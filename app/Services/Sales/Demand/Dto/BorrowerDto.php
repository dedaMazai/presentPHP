<?php

namespace App\Services\Sales\Demand\Dto;

use App\Models\Role;
use App\Models\Sales\FamilyStatus;
use App\Models\Sales\OwnerType;
use Carbon\Carbon;

/**
 * Class BorrowerDto
 *
 * @package App\Services\Sales\Demand\Dto
 */
class BorrowerDto
{
    public function __construct(
        public string $id,
        public string $jointOwnerId,
        public string $fullName,
    ) {
    }
}
