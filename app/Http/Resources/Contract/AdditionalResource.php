<?php

namespace App\Http\Resources\Contract;

use App\Http\Resources\Meter\MeterEnterPeriodResource;
use App\Models\V2\Contract\Contract;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AdditionalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        /** @var Contract $this */
        return [
            'id' => $this->getId(),
            'date' => $this->getDate()->format('d.m.Y'),
            'name' => $this->getService()->value == '030080'?"Набор дополнительных опций":$this->getArticleOrders()[0]->getName(),
            'is_active' => $this->getRegistrationNumber() == null,
        ];
    }
}
