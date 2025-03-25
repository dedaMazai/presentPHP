<?php

namespace App\Services\Mortgage\Dto;

/**
 * Class GetLoanOffersRequestDto
 *
 * @package App\Services\Mortgage\Dto
 */
class GetLoanOffersRequestDto
{
    public function __construct(
        public int $age,
        public string $agendaType,
        public int $cost,
        public int $housingComplexId,
        public int $initialPayment,
        public bool $isInsured,
        public bool $isRfCitizen,
        public int $loanPeriod,
        public int $lastJobExp,
        public int $overallExp,
        public string $mortgageType,
        public string $proofOfIncome,
        public string $employmentType,
        public ?int $payrollProgramBankId,
    ) {
    }
}
