<?php

namespace App\Http\Resources\Meter;

use Illuminate\Http\Resources\Json\JsonResource;

class MeterResource extends JsonResource
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
            'id' => $this->getId(),
            'type' => $this->getType()->value,
            'subtype' => $this->getSubtype()?->value,
            'data_input_type' => $this->getDataInputType()->value,
            'number' => $this->getNumber(),
            'is_previous_value_calculated_by_standard' => $this->getIsPreviousValueCalculatedByStandard(),
            'is_values_entered_in_current_period' => $this->getIsValuesEnteredInCurrentPeriod(),
            'date_verification' => $this->getDateVerification()?->toDateString(),
            'values' => new MeterValueCollection($this->getValues()),
            'name' => $this->getName()?->name,
        ];
    }
}
