<?php

namespace App\Http\Resources;

use App\Models\Document\Document;
use App\Models\DocumentsName;
use Illuminate\Http\Resources\Json\JsonResource;

class JointOwnerDocumentInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var Document $this */
        return [
            'id' => $this->getId(),
            'document_status' => intval($this->getProcessingStatus()->value),
            'reason_failure' => $this->getReasonFailure(),
        ];
    }
}
