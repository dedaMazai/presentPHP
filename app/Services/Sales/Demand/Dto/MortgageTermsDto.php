<?php

namespace App\Services\Sales\Demand\Dto;

use Carbon\Carbon;

/**
 * Class MortgageTermsDto
 *
 * @package App\Services\Sales\Demand\Dto
 */
class MortgageTermsDto
{
    public function __construct(
        public ?bool $isDigital,
        public ?string $approvalId,
        public ?string $bankId,
        public ?Carbon $approvalDate,
        public ?bool $isManagerNotExist,
        public ?string $managerFio,
        public ?string $managerPhone,
    ) {
    }
}
