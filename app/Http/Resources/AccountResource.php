<?php

namespace App\Http\Resources;

use App\Http\Resources\Meter\MeterEnterPeriodResource;
use App\Models\Account\Account;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        /** @var Account $this */

        return [
            'number' => $this->getNumber(),
            'role' => $this->role ?? "",
            'realty_type' => $this->getRealtyType()->value,
            'address' => $this->getAddress(),
            'address_id' => $this->getAddressId(),
            'balance' => $this->getBalance(),
            'services_debt' => $this->getServicesDebt(),
            'service_seller_id' => $this->getServiceSeller()->getId(),
            'not_paid_months' => $this->getNotPaidMonths(),
            'is_meter_enter_period_active' => $this->getIsMeterEnterPeriodActive(),
            'project_id' => $this->getUkProject()?->id,
            'project_name' => $this->getUkProject()?->name,
            'project_crm_1c_id' => $this->getUkProject()?->crm_1c_id,
            'meter_enter_period' => new MeterEnterPeriodResource($this->getMeterEnterPeriod()),
            'build_id' => $this->getBuildId(),
            'address_number' => $this->getAddressNumber(),
            'uk_emergency_claim_phone' => $this->getUkEmergencyClaimPhone() ?? '+74952121060'
        ];
    }
}
