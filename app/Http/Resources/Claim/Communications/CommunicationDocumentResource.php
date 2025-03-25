<?php

namespace App\Http\Resources\Claim\Communications;

use Illuminate\Http\Resources\Json\JsonResource;

class CommunicationDocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'document_id' => $this['document_id'],
            'is_read' => $this['is_read'],
        ];
    }
}
