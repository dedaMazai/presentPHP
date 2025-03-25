<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RelationshipInviteResource extends JsonResource
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
            'account_number' => $this->account_number,
            'role' => $this->role->value,
            'accepted_at' => $this->accepted_at?->toDateTimeString(),
            'accepted_by' => $this->accepted_by,
            'created_at' => $this->created_at?->toDateTimeString(),
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'birth_date' => $this->birth_date->toDateString(),
        ];
    }
}
