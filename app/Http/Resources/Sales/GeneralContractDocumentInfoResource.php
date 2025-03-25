<?php

namespace App\Http\Resources\Sales;

use App\Models\Document\Document;
use Illuminate\Http\Resources\Json\JsonResource;

class GeneralContractDocumentInfoResource extends JsonResource
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
            'id' => $this?->getId(),
            'document_status' => $this->getProcessingStatus()?->value ? intval($this->getProcessingStatus()?->value) : null,
            'reason_failure' => strval($this->getReasonFailure()),
        ];
    }
}
