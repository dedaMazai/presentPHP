<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionLogResource extends JsonResource
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
            'account_number' => $this->account_number,
            'payment_method_type' => $this->payment_method_type->value,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'amount' => round($this->amount*100, 2),
            'claim_id' => $this->claim_id,
            'claim_number' => $this->claim_number,
            'claim_category_name' => $this->claim_category_name,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
