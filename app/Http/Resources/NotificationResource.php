<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $notification = $this['notification'];
        $user = $this['user'];

        return [
            'id' => $notification->id,
            'title' => $notification->title,
            'text' => $notification->text,
            'is_viewed' => $user ? $notification->isViewed($user) : null,
            'created_at' => $notification->created_at?->toDateTimeString(),
            'action' => [
                'type' => $notification->action?->type,
                'payload' => $notification->action?->payload,
            ]
        ];
    }
}
