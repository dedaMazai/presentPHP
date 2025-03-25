<?php

namespace App\Http\Resources\Contract;

use App\Http\Resources\Meter\MeterEnterPeriodResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $date = $this['date']?new Carbon($this['date']):null;

        return [
            'number' => intval($this['number']),
            'sum' => $this['sum'],
            'date' => $date?->format('d.m.Y')
        ];
    }
}
