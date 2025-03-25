<?php

namespace App\Http\Resources\V2;

use App\Http\Resources\Sales\GeneralContractDocumentInfoResource;
use App\Models\Document\Document;
use App\Models\DocumentsName;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $documents = new UserDocumentCollection($this['document']);
        $documents = $documents->jsonSerialize();

        foreach ($documents as $key => $document) {
            $documents[$key]['object_code'] = $this['user']->crm_id;
        }

        return $documents;
    }
}
