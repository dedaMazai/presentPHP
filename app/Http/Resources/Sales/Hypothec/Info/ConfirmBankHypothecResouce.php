<?php

namespace App\Http\Resources\Sales\Hypothec\Info;

use App\Models\Sales\MortgageApproval\MortgageApproval;
use Illuminate\Http\Resources\Json\JsonResource;

class ConfirmBankHypothecResouce extends JsonResource
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
            'name' => null,
            'credit_period' => null,
            'rate' => null,
            'month_payment' => null,
        ];
    }
}
