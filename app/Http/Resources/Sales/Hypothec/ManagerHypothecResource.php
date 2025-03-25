<?php

namespace App\Http\Resources\Sales\Hypothec;

use Illuminate\Http\Resources\Json\JsonResource;

class ManagerHypothecResource extends JsonResource
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
            'first_name' => $this['firstName'],
            'last_name' => $this['lastName'],
            'middle_name' => $this['middleName'],
            'phone' => $this['phone'],
            'email' => $this['email'],
        ];
    }
}
