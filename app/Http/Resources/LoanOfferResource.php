<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoanOfferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'bank_id' => $this->bankId,
            'bank_logo' => $this->bankLogo,
            'bank_name' => $this->bankName,
            'id' => $this->id,
            'max_age' => $this->maxAge,
            'max_credit_amount' => $this->maxCreditAmount,
            'max_credit_period' => $this->maxCreditPeriod,
            'min_age' => $this->minAge,
            'min_initial_payment' => $this->minInitialPayment,
            'min_last_job_exp' => $this->minLastJobExp,
            'min_overall_exp' => $this->minOverallExp,
            'name' => $this->name,
            'period_params' => $this->periodParams,
            'periods' => $this->periods,
            'rate' => $this->rate,
            'realty_cost_increase_percent' => $this->realtyCostIncreasePercent,
            'recommended_income_coeff' => $this->recommendedIncomeCoeff,
            'strictly_matches_loan_period' => $this->strictlyMatchesLoanPeriod,
        ];
    }
}
