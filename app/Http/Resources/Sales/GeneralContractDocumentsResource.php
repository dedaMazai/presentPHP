<?php

namespace App\Http\Resources\Sales;

use App\Models\Document\Document;
use App\Models\DocumentsName;
use Illuminate\Http\Resources\Json\JsonResource;

class GeneralContractDocumentsResource extends JsonResource
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
        $documentsName = DocumentsName::where('code', $this->getType()?->value)->first();

        return [
            'name' => $documentsName?$documentsName->name:'Документ',
            'description' => $documentsName?->description != '' ? $documentsName?->description : null,
            'document_type' => intval($this->getType()->value),
            'object_type_code' => $documentsName?->object_type_code,
            'object_code' => strval($this->getObjectCode()),
            'document_info' => $this->getId() != null ? new GeneralContractDocumentInfoResource($this) : null,
        ];
    }
}
