<?php

namespace App\Http\Resources\V2\Sales\Contract\JointOwner;

use App\Http\Resources\Meter\MeterEnterPeriodResource;
use App\Models\Sales\Customer\Customer;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class SignInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $date = Carbon::parse($this->getEsValidityDate()) > Carbon::now();
        /** @var Customer $this */
        return [
            'is_released' => (bool)$this->getEsValidityDate(),
            'validity_date' => $this->getEsValidityDate()?->format('d.m.Y H:i:s'),
            'code' => $this->getSignStatus()? intval($this->getSignStatus()->value) : null,
            'name' => $this->getSignStatus()?->label,
            'is_validate' => $date,
        ];
    }
}
