<?php

namespace App\Http\Resources\Sales\Hypothec\Info;

use App\Models\Sales\Hypothec\Hypothec;
use Illuminate\Http\Resources\Json\JsonResource;

class HypothecInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var Hypothec $this */
        return [
            'is_mortgage_online_available' => $this->getIsMortgageOnlineAvailable(),
            'type_hypothec_demand' => $this->getTypeHypothecDemand(),
            'is_manual_approve' => $this->getIsManualApprove() ?? false,
            'is_digital_approve' => $this->getIsDigitalApprove(),
            'approval_list' => $this->getApprovalList() != null ? new HypothecApprovalListCollection($this->getApprovalList()) : [],
            'confirm_bank_hypothec' => new ConfirmBankHypothecResouce($this->getConfirmBankHypothec()),
            'is_borrowers_availible' => $this->getIsBorrowersAvailible(),
            'borrowers' => $this->getBorrowers(),
            'mortgage_online_info' => $this->getMortageOnlineInfo(),
            'escrow_bank_name' => $this->getEscrowBankName(),
            'bank_name_approve_manual' => $this->getBankNameApproveManual() != 'Другой банк' ? $this->getBankNameApproveManual() : null
        ];
    }
}
