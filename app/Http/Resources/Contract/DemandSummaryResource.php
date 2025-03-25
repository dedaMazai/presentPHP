<?php

namespace App\Http\Resources\Contract;

use App\Http\Resources\Meter\MeterEnterPeriodResource;
use App\Services\Sales\Demand\Dto\SummaryDto;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class DemandSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var SummaryDto $this */
        return [
            'payment_mode' => $this?->paymentMode,
            'escrow_bank_name' => $this?->escrowBankName,
            'letter_of_credit_bank' => $this?->letterOfCreditBank,
            'type_of_ownership' => $this?->typeOfOwnership,
            'joint_owners' => $this?->jointOwners,
            'borrowers' => $this?->borrowers,
            'decoration' => $this?->decoration,
            'deponent' => $this?->deponent,
        ];
    }
}
