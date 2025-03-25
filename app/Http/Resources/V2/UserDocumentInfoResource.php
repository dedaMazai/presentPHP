<?php

namespace App\Http\Resources\V2;

use App\Http\Resources\Sales\GeneralContractDocumentInfoResource;
use App\Models\Document\Document;
use App\Models\DocumentsName;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDocumentInfoResource extends JsonResource
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
            'document_status' => $this->getProcessingStatus()?->value? intval($this->getProcessingStatus()->value): null,
            'reason_failure' => $this->getReasonFailure(),
        ];
    }
}
