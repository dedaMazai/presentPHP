<?php

namespace App\Http\Resources\V2\Sales\Deponent;

use App\Models\DocumentsName;
use App\Services\V2\Sales\Demand\Dto\Deponent\DeponentFizIdDto;
use App\Services\V2\Sales\Demand\Dto\Deponent\DeponentUrIdDto;
use Illuminate\Http\Resources\Json\JsonResource;

class DepositorInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        if ($this->resource instanceof DeponentUrIdDto) {
            return [
                'id' => $this?->id,
                'joint_owner_id' => $this?->joint_owner_id,
                'last_name' => $this?->name,
            ];
        } elseif ($this->resource instanceof DeponentFizIdDto) {
            return [
                'id' => $this?->id,
                'joint_owner_id' => $this?->joint_owner_id,
                'last_name' => $this?->first_name,
                'first_name' => $this?->last_name,
                'middle_name' => $this?->middle_name,
            ];
        }
    }
}
