<?php

namespace App\Http\Resources\Sales;

use App\Models\V2\Sales\SaleStage;
use Illuminate\Http\Resources\Json\JsonResource;

class StageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var SaleStage $this */
        return [
            'number' => $this->getNumber(),
            'name' => $this->getName(),
            'status' => $this->getStatus(),
            'substages' => new SubstageCollection($this->getSubstages()),
            'message' => $this->getMessage(),
            'icon' => $this->getIcon(),
            'code' => $this->getCode(),
        ];
    }
}
