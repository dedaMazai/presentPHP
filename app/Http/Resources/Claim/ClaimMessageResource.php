<?php

namespace App\Http\Resources\Claim;

use Illuminate\Http\Resources\Json\JsonResource;

class ClaimMessageResource extends JsonResource
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
            'id' => $this->getId(),
            'text' => $this->getText(),
            "message_type" => "message",
            'message_date' => $this->getMessageDate()->toDateTimeString(),
            'type' => $this->getType()->value,
            'sender_name' => $this->getSenderName(),
            'sender_position' => $this->getSenderPosition(),
            'is_read' => $this->getIsRead()
        ];
    }
}
