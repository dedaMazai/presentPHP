<?php

namespace App\Http\Resources\Deal;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentTypeResource extends JsonResource
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
            "object_price" => intval($this->getArticlePrice()),
            "total_price" => intval($this->getSumOpportunityMinusDiscount()),
            "delta_price" => $this->getArticlePrice() - $this->getSumOpportunityMinusDiscount()
        ];
    }
}
