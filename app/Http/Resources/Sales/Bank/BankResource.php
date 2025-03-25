<?php

namespace App\Http\Resources\Sales\Bank;

use Illuminate\Http\Resources\Json\JsonResource;

class BankResource extends JsonResource
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
            'name_short' => $this->getNameShort(),
            'name_full' => $this->getNameFull(),
            'type' => $this->getType()->value,
            'append_info' => $this->getAppendInfo() ? new BankInfoResource($this->getAppendInfo()):null
        ];
    }
}
