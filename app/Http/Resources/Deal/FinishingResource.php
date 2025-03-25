<?php

namespace App\Http\Resources\Deal;

use Illuminate\Http\Resources\Json\JsonResource;

class FinishingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this['images'] != null) {
            foreach ($this['images'] as $image) {
                $images[] = ['url' => $image];
            }
        } else {
            $images = [];
        }

        return [
            "id" => $this['id'],
            "name" => $this['name'],
            "image" => $images,
            "is_selected" => $this['isSelected'],
            "catalog" => $this['catalog'],
        ];
    }
}
