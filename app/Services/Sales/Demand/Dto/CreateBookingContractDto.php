<?php

namespace App\Services\Sales\Demand\Dto;

use App\Models\Sales\Demand\DemandBookingType;
use App\Models\User\User;
use App\Services\Sales\Property\Dto\PropertyBookingDto;

/**
 * Class CreateBookingContractDto
 *
 * @package App\Services\Sales\Demand\Dto
 */
class CreateBookingContractDto
{
    public function __construct(
        public string $demandId,
        public DemandBookingType $bookingType,
        public PropertyBookingDto $propertyBookingDto,
        public User $user,
    ) {
    }
}
