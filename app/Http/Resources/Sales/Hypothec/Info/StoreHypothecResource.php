<?php

namespace App\Http\Resources\Sales\Hypothec\Info;

use App\Models\Sales\Hypothec\Hypothec;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreHypothecResource extends JsonResource
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
            'demaid_id' => $this->getIsMortgageOnlineAvailable(),
            'id' => $this->getTypeHypothecDemand(),
            'manager_hyphotec' => $this->getIsManualApprove(),
        ];
    }
}
