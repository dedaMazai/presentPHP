<?php

namespace App\Http\Resources\Contract;

use App\Http\Resources\Meter\MeterEnterPeriodResource;
use App\Http\Resources\Sales\StageCollection;
use App\Models\V2\Contract\Contract;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailAdditionalContractResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $serviceMain = [
            '090010' => 'bti',
            '090020' => 'correction',
            '030080' => 'add_option',
            '030090' => 'furniture',
        ];

        /** @var Contract $this */
        return [
            'id' => $this['additionalContract']->getId(),
            'date' => $this['additionalContract']->getDate()->format('d.m.Y'),
            'name' => $this['additionalContract']->getArticleOrders()[0]->getName(),
            'sum' => $this['additionalContract']->getEstimated(),
            'is_active' => $this['additionalContract']->getRegistrationNumber() == null,
            'agreement_type' => $serviceMain[$this['additionalContract']->getService()->value],
            'is_electro_reg' => !($this['additionalContract']->getElectroReg() == 'no'),
            'electro_reg_info' => $this['additionalContract']->getElectroRegInfo(),
            'orders' => $this['orders'],
            'stages' => new StageCollection($this['stages']),
        ];
    }
}
