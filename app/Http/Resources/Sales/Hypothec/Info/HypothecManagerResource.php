<?php

namespace App\Http\Resources\Sales\Hypothec\Info;

use Illuminate\Http\Resources\Json\JsonResource;

class HypothecManagerResource extends JsonResource
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
            'first_name' => $this->getIsMortgageOnlineAvailable(),
            'last_name' => $this->getTypeHypothecDemand(),
            'middle_name' => $this->getIsManualApprove(),
            'phone' => $this->getIsManualApprove(),
            'email' => $this->getIsManualApprove(),
        ];
    }
}
