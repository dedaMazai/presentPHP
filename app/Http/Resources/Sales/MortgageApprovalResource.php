<?php

namespace App\Http\Resources\Sales;

use Illuminate\Http\Resources\Json\JsonResource;

class MortgageApprovalResource extends JsonResource
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
            'bank_id' => $this->getBankId(),
            'bank_name' => $this->getBankName(),
            'decision_type' => $this->getDecisionType()?->value,
            'rate' => $this->getRate(),
            'period' => $this->getPeriod(),
            'initial_payment' => $this->getInitialPayment(),
        ];
    }
}
