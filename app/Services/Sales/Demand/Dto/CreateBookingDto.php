<?php

namespace App\Services\Sales\Demand\Dto;

use App\Models\Sales\Demand\DemandBookingType;
use App\Models\Sales\Demand\DemandType;
use App\Models\User\User;
use App\Services\Sales\Property\Dto\PropertyBookingDto;

/**
 * Class CreateBookingDto
 *
 * @package App\Services\Sales\Demand\Dto
 */
class CreateBookingDto
{
    public function __construct(
        public DemandType $type,
        public DemandBookingType $bookingType,
        public PropertyBookingDto $propertyBookingDto,
        public User $user,
    ) {
    }
}
