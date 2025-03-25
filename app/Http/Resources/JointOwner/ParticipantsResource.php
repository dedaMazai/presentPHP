<?php

namespace App\Http\Resources\JointOwner;

use App\Http\Resources\Meter\MeterEnterPeriodResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ParticipantsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $gender = null;

        if ($this['gender'] == 'male') {
            $gender = 1;
        } elseif ($this['gender'] == 'female') {
            $gender = 2;
        }

        return [
            'user_id' => strval($this['id']),
            'first_name' => $this['first_name'],
            'last_name' => $this['last_name'],
            'middle_name' => $this['middle_name'],
            'gender' => $gender,
            'birth_date' => $this['birth_date'],
            'crm_id' => $this['crm_id'] ?? null
        ];
    }
}
