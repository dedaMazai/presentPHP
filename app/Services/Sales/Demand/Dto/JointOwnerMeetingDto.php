<?php

namespace App\Services\Sales\Demand\Dto;

use App\Models\Role;
use App\Models\Sales\OwnerType;
use Carbon\Carbon;

/**
 * Class JointOwnerMeetingDto
 *
 * @package App\Services\Sales\Demand\Dto
 */
class JointOwnerMeetingDto
{
    public function __construct(
        public ?string $id,
        public ?string $part,
        public ?Role $role,
        public ?OwnerType $ownerType,
        public ?string $ownerTypeComment,
        public ?string $addressCourier,
        public ?Carbon $meetingDate,
    ) {
    }
}
