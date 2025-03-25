<?php

namespace App\Http\Resources\V2\Sales\Contract;

use App\Models\Document\Document;
use App\Models\DocumentsName;
use Illuminate\Http\Resources\Json\JsonResource;

class DraftContractDocumentResource extends JsonResource
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
        $document = DocumentsName::where('code', '=', $this->getType()->value)->first();

        return [
            'name' => $document?$document->name:'Документ',
            'id' => $this->getId(),
            'description' => $document?->description,
        ];
    }
}
