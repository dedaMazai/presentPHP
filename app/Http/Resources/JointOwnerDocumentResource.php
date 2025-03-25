<?php

namespace App\Http\Resources;

use App\Models\DocumentsName;
use Illuminate\Http\Resources\Json\JsonResource;

class JointOwnerDocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $documentsName = DocumentsName::where('code', $this['document']->getType()->value)->first();
        return [
            'name' => $documentsName?$documentsName->name:'Документ',
            'description' => $documentsName?->description,
            'document_type' => intval($this['document']->getType()?->value),
            'object_type_code' => $documentsName?->object_type_code,
            'object_code' => $this['jointOwnerId'],
            'document_info' => $this['document']->getId()?new JointOwnerDocumentInfoResource($this['document']):null,
        ];
    }
}
