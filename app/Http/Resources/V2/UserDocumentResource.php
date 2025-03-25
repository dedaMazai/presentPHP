<?php

namespace App\Http\Resources\V2;

use App\Http\Resources\Sales\GeneralContractDocumentInfoResource;
use App\Models\Document\Document;
use App\Models\DocumentsName;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDocumentResource extends JsonResource
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
            'document_type' => $this->getType() ? intval($this->getType()->value) : null,
            'object_type_code' => $document?->object_type_code,
            'object_code' => $document?->object_type_code,
            'document_info' => $this->getId() ? new UserDocumentInfoResource($this) : null,
        ];
    }
}
