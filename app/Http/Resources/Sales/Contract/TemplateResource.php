<?php

namespace App\Http\Resources\Sales\Contract;

use App\Models\Contract\ContractDocument;
use App\Models\Document\Document;
use App\Models\DocumentsName;
use Illuminate\Http\Resources\Json\JsonResource;

class TemplateResource extends JsonResource
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
            'name' => $document?->name,
            'id' => $this->getId(),
            'description' => $document?->description,
        ];
    }
}
