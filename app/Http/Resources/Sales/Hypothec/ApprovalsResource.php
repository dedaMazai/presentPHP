<?php

namespace App\Http\Resources\Sales\Hypothec;

use Illuminate\Http\Resources\Json\JsonResource;

class ApprovalsResource extends JsonResource
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
            'id' => $this['id'],
            'name' => $this['name'],
            'hypothec_bank_id' => $this['hypothecBankId'],
            'hypothec_type' => new HypothecTypeResource($this['hypothecType']),
            'sum_approval' => $this['sumApproval'],
            'step_date' => $this['stepDate'],
            'sum_approved' => $this['sumApproved'],
            'is_accreditation' => $this['isAccreditation'],
            'is_select_bank' => $this['isSelectBank'],
            'bank_decision' => new BankDecisionResource($this['bankDecision']),
            'step_conveyor' => $this['stepСonveyor'],
            'description' => $this['description'],
            'dh_status_code' => $this['dhStatusCode'],
            'dh_contract_bank_guid' => $this['dhContractBankGuid'],
            'dh_crediting_period' => $this['dhCreditingPeriod'],
            'dh_initial_payment' => $this['dhInitialPayment'],
            'dhRate' => $this['dhRate'],
            'dh_bank_employe_email' => $this['dhBankEmployeEmail'],
            'dh_comment' => $this['dhComment'],
        ];
    }
}
