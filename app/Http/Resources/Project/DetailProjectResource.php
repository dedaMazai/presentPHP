<?php

namespace App\Http\Resources\Project;

use App\Http\Resources\Article\ArticleCollection;
use App\Http\Resources\ImageCollection;
use App\Http\Resources\ImageResource;
use App\Http\Resources\MortgageProgramCollection;
use App\Http\Resources\ShowcaseImageResource;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailProjectResource extends JsonResource
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
            'id' => $this['project']->id,
            'name' => $this['project']->name,
            'metro' => $this['project']->metro,
            'metro_color' => $this['project']->metro_color,
            'crm_ids' => $this['project']->crm_ids,
            'mortgage_calculator_id' => $this['project']->mortgage_calculator_id,
            'lat' => $this['project']->lat,
            'long' => $this['project']->long,
            'office_phone' => $this['project']->office_phone,
            'office_address' => $this['project']->office_address,
            'office_lat' => $this['project']->office_lat,
            'office_long' => $this['project']->office_long,
            'office_work_hours' => $this['project']->office_work_hours,
            'property_type_params' => $this['project']->property_type_params,
            'showcase_image' => new ShowcaseImageResource($this['project']->showcaseImage),
            'color' => $this['project']->color,
            'description' => $this['project']->description,
            'city_id' => $this['project']->city_id,
            'mortgage_types' => $this['project']->mortgage_types,
            'payroll_bank_programs' => $this['project']->payroll_bank_programs,
            'mortgage_min_property_price' => $this['project']->mortgage_min_property_price,
            'mortgage_max_property_price' => $this['project']->mortgage_max_property_price,
            'min_property_price' => $this['project']->min_property_price ?
                (float)number_format($this['project']->min_property_price / 1000000, 2) : null,
            'max_property_price' => $this['project']->max_property_price ?
                (float)number_format($this['project']->max_property_price / 1000000, 2) : null,
            'type' => [
                'name' => $this['project']->type->name,
                'projects' => new ProjectCollection($this['project']->type->projects)
            ],
            'main_image' => new ImageResource($this['project']->mainImage),
            'map_image' => $this['project']->mapImage ? new ImageResource($this['project']->mapImage) : null,
            'images' => new ImageCollection($this['project']->images),
            'articles' => new ArticleCollection($this['project']->articles),
            'galleries' => new ImageCollection($this['project']->images),
            'crm_addresses' => new ProjectAddressCollection($this['address']),
            'mortgage_programs' => new MortgageProgramCollection($this['project']->mortgagePrograms)
        ];
    }
}
