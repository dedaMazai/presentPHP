<?php

namespace App\Http\Resources\Sales\Contract;

use App\Models\Contract\ContractDocument;
use Illuminate\Http\Resources\Json\JsonResource;

class ConfidantResource extends JsonResource
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
            'id' => $this['jointOwner']->getId(),
            'joint_owner_id' => $this['jointOwner']->getJointOwnerId(),
            'full_name' => $this['jointOwner']->getFullName(),
            'templates' => new TemplateCollection($this['templates']),
            'documents' => new DocumentCollection($this['documents']),
        ];
    }
}
