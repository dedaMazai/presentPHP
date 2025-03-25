<?php

namespace App\Http\Resources\V2\Sales\JointOwner;

use Illuminate\Http\Resources\Json\JsonResource;

class SignatoryResource extends JsonResource
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
            'id'        => $this->getId() ?? null,
            'full_name' => $this->getFullName() ?? null,
            'label'     => $this->getLabel() ?? null,
            'signatory' => $this->getSignatory() ?? null,
        ];
    }
}
