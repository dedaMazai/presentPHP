<?php

namespace App\Http\Resources\Claim\Communications;

use Illuminate\Http\Resources\Json\JsonResource;

class CommunicationResource extends JsonResource
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
            'messages' => isset($this['messages'])?new CommunicationMessageCollection($this['messages']):[],
            'documents' => isset($this['documents'])?new CommunicationDocumentCollection($this['documents']):[],
        ];
    }
}
