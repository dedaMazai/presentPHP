<?php

namespace App\Http\Resources\Sales\Demand\PaymentPlan;

use App\Http\Resources\Sales\PropertyResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class DemandPaymentPlanResource extends JsonResource
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
            'number' => $this['number'],
            'sum' => $this['sum'],
            'date' => Carbon::parse($this['date'])->format('d.m.Y'),
        ];
    }
}
