<?php

namespace App\Http\Resources\News;

use App\Http\Resources\ContentItemCollection;
use App\Http\Resources\ContentItemResource;
use App\Http\Resources\ImageResource;
use App\Http\Resources\UkProjectResource;
use App\Models\UkProject;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsCategoryResource extends JsonResource
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
            'category' => $this,
            'name' => $this[''],
        ];
    }
}
