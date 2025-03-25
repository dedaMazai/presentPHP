<?php

namespace App\Http\Resources\LegalPerson;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountAddResource extends JsonResource
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
            'contact_id' => $this['jointOwners'][0]['accountId'],
            'joint_owner_id' => $this['jointOwners'][0]['id'],
        ];
    }
}
