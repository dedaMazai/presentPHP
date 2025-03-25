<?php

namespace App\Http\Resources\User;

use App\Models\Sales\Customer\Customer;
use App\Models\User\UserSignInfo;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var Customer $this */
        $date = Carbon::parse($this->getEsValidityDate()) > Carbon::now();

        return [
            'is_released' => $this?->getEsValidityDate() ?? false,
            'validity_date' => $this->getEsValidityDate()?->format('dd.mm.yyyy h:i:S'),
            'code' => $this->getSignStatus()?->value,
            'name' => $this->getSignStatus()?->label,
            'is_validate' => $date,
        ];
    }
}
