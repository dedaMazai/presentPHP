<?php

namespace App\Http\Resources\Sales\Deponent;

use Illuminate\Http\Resources\Json\JsonResource;

class DepositorDocumentInfoResource extends JsonResource
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
            'id' => $this['id'],
            'document_status' => $this['documentProcessingStatus']['code'] ? intval($this['documentProcessingStatus']['code']) : null,
            'reason_failure' => $this['reasonFailure'] ?? null,
        ];
    }
}
