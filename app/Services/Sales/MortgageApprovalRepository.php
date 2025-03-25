<?php

namespace App\Services\Sales;

use App\Models\Sales\Demand\Demand;
use App\Models\Sales\MortgageApproval\MortgageApproval;
use App\Models\Sales\MortgageApproval\MortgageApprovalDecisionType;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;

/**
 * Class MortgageApprovalRepository
 *
 * @package App\Services\Sales
 */
class MortgageApprovalRepository
{
    public function __construct(
        private DynamicsCrmClient $dynamicsCrmClient,
    ) {
    }

    /**
     * @return MortgageApproval[]
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getAllByDemand(Demand $demand): array
    {
        $data = $this->dynamicsCrmClient->getDemandMortgageApprovals($demand->getId());

        return array_map(fn($data) => $this->makeMortgageApproval($data), $data['approvalList']);
    }

    public function makeMortgageApproval(array $data): MortgageApproval
    {
        return new MortgageApproval(
            id: $data['id'],
            name: $data['name'] ?? null,
            bankId: $data['hypothecBankId'] ?? null,
            bankName: $data['hypothecBankFormated'] ?? null,
            decisionType: MortgageApprovalDecisionType::tryFrom($data['bankDecision']['code'] ?? ''),
            rate: $data['dhRate'] ?? null,
            period: $data['dhCreditingPeriod'] ?? null,
            initialPayment: $data['dhInitialPayment'] ?? null,
            creditingPeriodApproved: $data['creditingPeriodApproved'] ?? null,
            rateApproved: $data['rateApproved'] ?? null,
            monthlyPaymentApproved: $data['monthlyPaymentApproved'] ?? null,
            dhStatusCode: $data['dhStatusCode'] ?? null,
            sumApproved: $data['sumApproved'] ?? null,
        );
    }
}
