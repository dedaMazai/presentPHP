<?php

namespace App\Http\Resources\V2\Sales\Demand;

use App\Models\V2\Sales\ArticleOrder;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesSchemeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var ArticleOrder $this */
        return [
            'code' => intval($this->getCode()),
            'name' => $this->getName(),
        ];
    }
}
