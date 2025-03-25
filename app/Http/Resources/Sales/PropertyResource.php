<?php

namespace App\Http\Resources\Sales;

use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
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
            'type' => $this->getType()?[
                'code' => $this->getType()->value,
                'name' => $this->getType()->label,
            ]:null,
            'variant' => $this->getVariant()?[
                'code' => $this->getVariant()->value,
                'name' => $this->getVariant()->label,
            ]:null,
            'status' => $this->getStatus()?[
                'code' => $this->getStatus()->value,
                'name' => $this->getStatus()->label,
            ]:null,
            'number' => intval($this->getNumber()),
            'layout_id' => $this->getLayoutId(),
            'layout_number' => intval($this->getLayoutNumber()),
            'floor' => $this->getFloor(),
            'rooms' => $this->getRooms(),
            'is_escrow' => $this->getIsEscrow(),
            'escrow_bank_id' => $this->getEscrowBankId(),
            'escrow_bank_name' => $this->getEscrowBankName(),
            'project_name' => $this->getProject()?->name,
            'plan' => $this->getPlan() ? new PropertyImageResource($this->getPlan()) : null,
            'plans' => $this->getPlans() ? new ObjectPlansResource($this->getPlans()) : null,
            'address' => $this->getAddress() ? new AddressResource($this->getAddress()): null,
            'contracts' => $this->getContracts() ? new ContractCollection($this->getContracts()): null,
            'instalments' => $this->getInstalments() ? new CharacteristicSaleCollection($this->getInstalments()): null,
            'finishes' => $this->getFinishes() ? new CharacteristicSaleCollection($this->getFinishes()): null
        ];
    }
}
