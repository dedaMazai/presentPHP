<?php

namespace App\Http\Resources\Sales;

use App\Models\V2\Sales\SaleSubstage;
use Illuminate\Http\Resources\Json\JsonResource;

class SubstageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var SaleSubstage $this */
        return [
            'number' => $this->getNumber(),
            'name' => $this->getName(),
            'status' => $this->getStatus(),
            'icon' => $this->getIcon(),
            'code' => $this->getCode(),
            'status_message' => $this->getStatusMessage(),
            'status_icon' => $this->getStatusIcon(),
            'icon_navbar' => new IconNavbarResource($this->getIconNavbar()),
        ];
    }
}
