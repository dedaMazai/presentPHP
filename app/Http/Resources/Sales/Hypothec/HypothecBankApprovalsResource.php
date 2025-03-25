<?php

namespace App\Http\Resources\Sales\Hypothec;

use Illuminate\Http\Resources\Json\JsonResource;

class HypothecBankApprovalsResource extends JsonResource
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
            'id' => $this['id']??null,
            'name' => $this['name']??null,
            'hypothec_bank_id' => $this['hypothecBankId']??null,
            'hypothec_bank_formated' => $this['hypothecBankFormated']??null,
            'hypothec_type' => new HypothecTypeResource($this['hypothecType']??null),
            'sum_approval' => $this['sumApproval']??null,
            'step_date' => $this['stepDate']??null,
            'sum_approved' => $this['sumApproved']??null,
            'is_accreditation' => $this['isAccreditation']??null,
            'is_select_bank' => $this['isSelectBank']??null,
            'bank_decision' => new BankDecisionResource($this['bankDecision']??null),
            'step_conveyor' => $this['stepСonveyor']??null,
            'description' => $this['description']??null,
            'dh_status_code' => $this['dhStatusCode']??null,
            'dh_contract_bank_guid' => $this['dhContractBankGuid']??null,
            'dh_crediting_period' => $this['dhCreditingPeriod']??null,
            'dh_initial_payment' => $this['dhInitialPayment']??null,
            'dh_initia_pPayment_formated' => $this['dhInitialPayment']??null,
            'dhRate' => $this['dhRate']??null,
            'dh_bank_employe_email' => $this['dhBankEmployeEmail']??null,
            'dh_comment' => $this['dhComment']??null,
        ];
    }
}
