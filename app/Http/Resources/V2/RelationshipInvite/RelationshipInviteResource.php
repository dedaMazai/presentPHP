<?php

namespace App\Http\Resources\V2\RelationshipInvite;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class RelationshipInviteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this['id'],
            'role_description' => $this['roleDescription'] ?? [],
            'first_name' => $this['firstName'],
            'last_name' => $this['lastName'],
            'phone' => $this['phone'],
            'birth_date' => Carbon::parse($this['birthDate'])->toDateString(),
        ];
    }
}
