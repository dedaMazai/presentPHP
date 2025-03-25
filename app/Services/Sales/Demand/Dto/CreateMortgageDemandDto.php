<?php

namespace App\Services\Sales\Demand\Dto;

use App\Models\Sales\Customer\Customer;
use App\Models\Sales\Demand\Demand;
use App\Models\Sales\Demand\DemandType;
use App\Models\Sales\Property\Property;

/**
 * Class CreateMortgageDemandDto
 *
 * @package App\Services\Sales\Demand\Dto
 */
class CreateMortgageDemandDto
{
    public function __construct(
        public Demand $demand,
        public DemandType $type,
        public Property $property,
        public Customer $customer,
    ) {
    }
}
