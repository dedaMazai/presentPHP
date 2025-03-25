<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MortgageProgramResource extends JsonResource
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
            'id' => $this->id,
            'initial_payment' => $this->initial_payment,
            'citizenship' => $this->citizenship,
            'period' => $this->period,
            'addresses' => $this->addresses,
            'bank_title' => $this->bankInfo->title,
            'bank_logo' => new ImageResource($this->bankInfo->logoImage),
        ];
    }
}
