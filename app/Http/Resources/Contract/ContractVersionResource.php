<?php

namespace App\Http\Resources\Contract;

use App\Models\Document;
use App\Models\DocumentsName;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractVersionResource extends JsonResource
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
        $documentName = DocumentsName::where('code', '=', $this->getType())->first();

        return [
            'name' => $documentName?->name,
            'id' => $this->getId(),
            'description' => $documentName?->description,
        ];
    }
}
