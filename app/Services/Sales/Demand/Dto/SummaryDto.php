<?php

namespace App\Services\Sales\Demand\Dto;

use App\Models\Sales\Demand\DemandBookingType;
use App\Models\User\User;
use App\Services\Sales\Property\Dto\PropertyBookingDto;

/**
 * Class SummaryDto
 *
 * @package App\Services\Sales\Demand\Dto
 */
class SummaryDto
{
    public function __construct(
        public ?string $paymentMode,
        public ?string $escrowBankName,
        public ?string $letterOfCreditBank,
        public ?string $typeOfOwnership,
        public ?array $jointOwners,
        public ?array $borrowers,
        public ?string $decoration,
        public ?string $deponent,
    ) {
    }
}
