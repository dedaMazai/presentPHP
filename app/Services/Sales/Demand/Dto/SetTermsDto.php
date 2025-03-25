<?php

namespace App\Services\Sales\Demand\Dto;

use App\Models\Sales\Demand\Demand;
use App\Models\Sales\PaymentMode;

/**
 * Class SetTermsDto
 *
 * @package App\Services\Sales\Demand\Dto
 */
class SetTermsDto
{
    public function __construct(
        public Demand $demand,
        public PaymentMode $paymentMode,
        public ?string $letterOfCreditBankId,
        public ?bool $isEscrowBankClient,
        public ?string $instalmentId,
        public ?MortgageTermsDto $mortgageTerms,
    ) {
    }
}
