<?php

namespace App\Http\Resources\V2\Sales\Deponent;

use App\Models\DocumentsName;
use Illuminate\Http\Resources\Json\JsonResource;

class DepositorDocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $documentsName = DocumentsName::where('code', 40015)->first();

        return [
            'name' => $documentsName?$documentsName->name:'Документ',
            'description' => $documentsName?->description,
            'document_type' => 40015,
            'object_type_code' => $documentsName?->object_type_code,
            'object_code' => $this['owner_id'],
            'document_info' => new DepositorDocumentInfoResource($this['depositor_document']['document_info']),
        ];
    }
}
