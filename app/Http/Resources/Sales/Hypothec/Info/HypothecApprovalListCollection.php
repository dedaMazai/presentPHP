<?php

namespace App\Http\Resources\Sales\Hypothec\Info;

use Illuminate\Http\Resources\Json\ResourceCollection;

class HypothecApprovalListCollection extends ResourceCollection
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
