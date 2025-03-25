<?php

namespace App\Http\Resources\Contract;

use App\Http\Resources\Meter\MeterEnterPeriodResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentPlanResource extends JsonResource
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
            'number' => $this['number'] ?? null,
            'sum' => isset($this['sum']) ? round($this['sum'], 2) : 0,
            'sum_payment' => isset($this['sumPayment']) ? round($this['sumPayment'], 2) : 0,
            'sum_debt' => isset($this['sumDebt']) ? round($this['sumDebt'], 2) : 0,
            'sign_pay' => $this['signPay'] ?? null,
            'number_days_delay' => $this['numberDaysDelay'] ?? null,
            'date' => $date?->format('d.m.Y')
        ];
    }
}
