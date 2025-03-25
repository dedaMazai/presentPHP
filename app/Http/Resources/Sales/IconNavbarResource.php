<?php

namespace App\Http\Resources\Sales;

use App\Models\V2\Sales\IconNavbar;
use Illuminate\Http\Resources\Json\JsonResource;

class IconNavbarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var IconNavbar $this */
        return [
            'number' => $this->getNumber(),
            'active_icon' => $this->getActiveIcon(),
            'disable_icon' => $this->getDisableIcon(),
        ];
    }
}
