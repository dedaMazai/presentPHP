<?php

namespace App\Services\Sales\Deal\Dto;

use App\Models\Sales\Demand\DemandBookingType;
use App\Models\Sales\Demand\DemandStatus;
use App\Models\User\User;
use App\Services\Sales\Property\Dto\PropertyBookingDto;
use Carbon\Carbon;

/**
 * Class CreateDealDto
 *
 * @package App\Services\Sales\Deal\Dto
 */
class CreateDealDto
{
    public function __construct(
        public User $user,
        public string $demandId,
        public PropertyBookingDto $propertyBookingDto,
        public DemandStatus $demandStatus,
        public DemandBookingType $demandBookingType,
        public Carbon $initialBeginDate,
        public Carbon $initialEndDate,
    ) {
    }
}
