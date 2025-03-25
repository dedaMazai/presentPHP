<?php

namespace App\Http\Resources\Sales\Bank;

use App\Http\Resources\ImageResource;
use Illuminate\Http\Resources\Json\JsonResource;

class BankInfoResource extends JsonResource
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
            'title' => $this->title,
            'logo_image' => new ImageResource($this->logoImage),
            'price' => $this->price,
            'link' => $this->link,
        ];
    }
}
