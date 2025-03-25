<?php

namespace App\Http\Resources\Claim\Communications;

use Illuminate\Http\Resources\Json\JsonResource;

class CommunicationMessageResource extends JsonResource
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
            'message_id' => $this['message_id'],
            'is_read ' => $this['is_read']
        ];
    }
}
