<?php

namespace App\Http\Resources\Sales\Hypothec\Info;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Sales\MortgageApproval\MortgageApproval;

class HypothecApprovalListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var MortgageApproval $this */
        return [
            'id' => $this->getId(),
            'bank_name' => $this->getName(),
            'credit_period' => $this->getPeriod(),
            'approve_credit_period' => $this->getCreditingPeriodApproved(),
            'rate' => $this->getRate(),
            'approve_rate' => $this->getRateApproved(),
            'month_payment' => $this->getInitialPayment(),
            'approve_month_payment' => $this->getMonthlyPaymentApproved(),
            'status' => $this->getDhStatusCode(),
            'approve_amount_credit' => $this->getSumApproved(),
        ];
    }
}
