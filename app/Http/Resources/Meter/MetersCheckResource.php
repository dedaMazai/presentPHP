<?php

namespace App\Http\Resources\Meter;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class MetersCheckResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $years = array_map(function ($date) {
            return Carbon::parse($date)->format('Y');
        }, $this['statisticYears']);

        return [
            'has_meters' => $this['hasMeters'],
            'has_statistic' => $this['hasStatistic'],
            'statistic_years' => $years
        ];
    }
}
