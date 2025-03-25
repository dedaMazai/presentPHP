<?php

namespace App\Http\Resources\Project;

use App\Http\Resources\ShowcaseImageResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'metro' => $this->metro,
            'metro_color' => $this->metro_color,
            'crm_ids' => $this->crm_ids,
            'mortgage_calculator_id' => $this->mortgage_calculator_id,
            'lat' => $this->lat,
            'long' => $this->long,
            'office_phone' => $this->office_phone,
            'office_address' => $this->office_address,
            'office_lat' => $this->office_lat,
            'office_long' => $this->office_long,
            'office_work_hours' => $this->office_work_hours,
            'property_type_params' => $this->property_type_params,
            'showcase_image' => new ShowcaseImageResource($this->showcaseImage),
            'color' => $this->color,
            'description' => $this->description,
            'city_id' => $this->city_id,
            'mortgage_types' => $this->mortgage_types,
            'payroll_bank_programs' => $this->payroll_bank_programs,
            'mortgage_min_property_price' => $this->mortgage_min_property_price,
            'mortgage_max_property_price' => $this->mortgage_max_property_price,
            'min_property_price' => $this->min_property_price ?
                (float)number_format($this->min_property_price / 1000000, 2) : null,
            'max_property_price' => $this->max_property_price ?
                (float)number_format($this->max_property_price / 1000000, 2) : null,
        ];
    }
}
