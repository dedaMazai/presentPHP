<?php

namespace App\Http\Resources\Contract\SignApp;

use App\Http\Resources\Meter\MeterEnterPeriodResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class SignAppUrlResource extends JsonResource
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
            'android' => $this['androidUrl'],
            'ios' => $this['iosUrl'],
        ];
    }
}
