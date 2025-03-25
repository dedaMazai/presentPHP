<?php

namespace App\Http\Resources\V2\Sales;

use App\Http\Resources\Sales\ObjectPlansResource;
use App\Models\V2\Sales\Property\Property;
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
        /** @var Property $this */
        return [
            'type' => $this->getType()?[
                'code' => intval($this->getType()->value),
                'name' => $this->getType()->label,
            ]:null,
            'variant' => $this->getVariant()?[
                'code' => intval($this->getVariant()->value),
                'name' => $this->getVariant()->label,
            ]:null,
            'status' => $this->getStatus()?[
                'code' => intval($this->getStatus()->value),
                'name' => $this->getStatus()->label,
            ]:null,
            'number' => intval($this->getNumber()),
            'layout_id' => $this->getLayoutId(),
            'layout_number' => intval($this->getLayoutNumber()),
            'floor' => $this->getFloor(),
            'rooms' => $this->getRooms(),
            'escrow_bank_name' => $this->getEscrowBankName(),
            'project_name' => $this->getProject()?->name ?? $this->getAddress()->getName(),
            'letter_of_credit_banks' => $this->getLetterOfCreditBanks() ? new LetterOfCreditBanksCollection($this->getLetterOfCreditBanks()): null,
            'plan' => $this->getPlan() ? new PropertyImageResource($this->getPlan()) : null,
            'plans' => $this->getPlans() ? new ObjectPlansResource($this->getPlans()) : null,
            'name_lk' => $this->getNameLk(),
            'address' => $this->getAddress() ? new AddressResource($this->getAddress()): null,
            'contracts' => $this->getContracts() ? new ContractCollection($this->getContracts()): null,
            'article_id' => $this->getId() ?? null,
            'is_escrow' => $this->getIsEscrow() ?? null,
//            'instalments' => $this->getInstalments() ? new CharacteristicSaleCollection($this->getInstalments()): null,
        ];
    }
}
