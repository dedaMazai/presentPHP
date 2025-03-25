<?php

namespace App\Http\Resources\Sales\Contract;

use App\Http\Resources\Sales\GeneralContractDocumentInfoResource;
use App\Models\Contract\ContractDocument;
use App\Models\Document\Document;
use App\Models\DocumentsName;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
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
        $document = DocumentsName::where('code', $this->getType()?->value)->first();

        return [
            'name' => $document?$document->name:'Документ',
            'description' => $document?->description,
            'document_type' => $this->getType()?->value ? intval($this->getType()?->value) : null,
            'object_type_code' => $document?->object_type_code,
            'object_code' => $this->getObjectCode(),
            'document_info' => $this->getId() != null ?new GeneralContractDocumentInfoResource($this):null,
        ];
    }
}
