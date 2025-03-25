<?php

namespace App\Http\Resources\Sales\Deponent;

use App\Models\DocumentsName;
use Illuminate\Http\Resources\Json\JsonResource;

class DepositorInfoResource extends JsonResource
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
            'id' => $this?->getContactId(),
            'joint_owner_id' => $this?->getContactId(),
            'last_name' => $this?->getLastName(),
            'first_name' => $this?->getFirstName(),
            'middle_name' => $this?->getMiddleName(),
        ];
    }
}
