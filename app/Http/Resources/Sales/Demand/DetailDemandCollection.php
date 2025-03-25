<?php

namespace App\Http\Resources\Sales\Demand;

use App\Http\Resources\Sales\CharacteristicSaleCollection;
use App\Http\Resources\Sales\CharacteristicSaleResource;
use App\Http\Resources\Sales\ContractCollection;
use App\Http\Resources\Sales\ContractResource;
use App\Http\Resources\Sales\CustomerCollection;
use App\Http\Resources\Sales\OwnerResource;
use App\Http\Resources\Sales\PropertyResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DetailDemandCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
