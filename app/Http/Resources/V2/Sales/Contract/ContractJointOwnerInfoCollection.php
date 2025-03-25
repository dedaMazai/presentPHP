<?php

namespace App\Http\Resources\V2\Sales\Contract;

use App\Http\Resources\Sales\JointOwner\ContractJointOwnerInfoResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Sales\JointOwner\ContractJointOwnerInfoCollection as ContractJointOwnerInfoCollectionV1p;

class ContractJointOwnerInfoCollection extends JsonResource
{
    public function toArray($request)
    {
        if (isset($this[0]->legalEntityData)) {
            return [
                'legalEntity' => $this[0]->legalEntityData,
                'individualOwner' => $this->when(
                    isset($this[0]->individualOwner),
                    function () use ($request) {
                        return new ContractJointOwnerInfoResource($this[0]->individualOwner);
                    }
                ),
            ];
        } else {
            return new ContractJointOwnerInfoCollectionV1p($this->resource);
        }
    }
}
