<?php

namespace App\Http\Resources\User;

use App\Models\Document\Document;
use App\Models\DocumentsName;
use Illuminate\Http\Resources\Json\JsonResource;

class SignInfoStatementDocumentResource extends JsonResource
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
        $documentName = DocumentsName::where('code', '=', '65538')->first();

        return [
            'name' => $documentName?->name,
            'id' => $this->getId(),
            'description' => $documentName?->description,
        ];
    }
}
