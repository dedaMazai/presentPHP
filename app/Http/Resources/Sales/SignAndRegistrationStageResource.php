<?php

namespace App\Http\Resources\Sales;

use App\Models\Contract\Contract;
use Illuminate\Http\Resources\Json\JsonResource;

class SignAndRegistrationStageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var array $this */

        return [
            'number' => $this['number'],
            'name' => $this['name'],
            'description' => $this['description'] ?? null,
            'is_date_available' => $this['is_date_available'] ?? null,
            'date' => $this['date'] ?? null,
            'date_title' => $this['date_title'] ?? null,
            'status' => $this['status'] ?? null,
        ];
    }
}
