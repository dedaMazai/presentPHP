<?php

namespace App\Http\Resources\Sales;

use Illuminate\Http\Resources\Json\JsonResource;

class ContractResource extends JsonResource
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
            'id' => $this->getId(),
            'name' => $this->getName(),
            'group' => $this->getGroup()->value,
            'date' => $this->getDate()?->toDateTimeString(),
            'estimated' => $this->getEstimated(),
            'status' => $this->getStatus()?->value,
            'step_name' => $this->getStepName(),
            'debt_plan_sum' => $this->getDebtPlanSum(),
            'percent_pay' => $this->getPercentPay(),
            'payment_mode' => $this->getPaymentModeCode()?->value,
            'sum_discount' => $this->getSumDiscount(),
            'registration_filing_date' => $this->getRegistrationFilingDate()?->toDateTimeString(),
            'registration_date' => $this->getRegistrationDate()?->toDateTimeString(),
            'base_finish_variant' => $this->getBaseFinishVariant() ?
                new CharacteristicSaleResource($this->getBaseFinishVariant()): null
        ];
    }
}
