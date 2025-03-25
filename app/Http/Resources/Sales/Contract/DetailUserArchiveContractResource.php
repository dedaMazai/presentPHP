<?php

namespace App\Http\Resources\Sales\Contract;

use App\Http\Resources\V2\Sales\ArchiveContractJointOwnerCollection;
use App\Http\Resources\V2\Sales\ArchivePropertyResource;
use App\Http\Resources\V2\Sales\JointOwner\GetJointOwnersCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailUserArchiveContractResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this['contract']->getId(),
            'contract_name' => $this['contract']->getName(),
            'contract_date' => $this['contract']->getDate()->format('d.m.Y'),
            'quantity' => $this['contract']->getArticleOrders()[0]->getQuantity(),
            'contract_price' => $this['contract']->getEstimated(),
            'property' => $this['property'] ? new ArchivePropertyResource($this['property']) : null,
            'joint_owners' => new GetJointOwnersCollection($this['jointOwners']),
        ];
    }
}
