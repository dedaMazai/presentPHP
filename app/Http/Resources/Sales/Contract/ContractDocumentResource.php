<?php

namespace App\Http\Resources\Sales\Contract;

use App\Models\Contract\ContractDocument;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractDocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var ContractDocument $this */
        return [
            'id' => $this->getId(),
            'joint_owner_id' => $this->getJointOwnerId(),
            'full_name' => $this->getFullName(),
            'templates' => new TemplateCollection($this->getTemplates()),
            'documents' => new DocumentCollection($this->getDocuments()),
        ];
    }
}
