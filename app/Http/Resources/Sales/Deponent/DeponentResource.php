<?php

namespace App\Http\Resources\Sales\Deponent;

use Illuminate\Http\Resources\Json\JsonResource;

class DeponentResource extends JsonResource
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
            'depositor_info' => new DepositorInfoResource($this['depositor_info']),
            'depositor_document' => new DepositorDocumentResource([
                'depositor_document' => $this['depositor_document'],
                'owner_id' => $this['owner_id']
            ]),
        ];
    }
}
