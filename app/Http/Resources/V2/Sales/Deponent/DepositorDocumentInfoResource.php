<?php

namespace App\Http\Resources\V2\Sales\Deponent;

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
            'joint_owner_id' => $this['joint_owner_id'] ?? null,
            'name' => $this['name'] ?? null,
            'document_status' => isset($this['documentProcessingStatus']['code'])
                ? intval($this['documentProcessingStatus']['code'])
                : null,
            'reason_failure' => $this['reasonFailure'] ?? null,
        ];
    }
}
