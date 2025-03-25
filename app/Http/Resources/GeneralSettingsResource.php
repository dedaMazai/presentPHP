<?php

namespace App\Http\Resources;

use App\Models\SupportTopics;
use Illuminate\Http\Resources\Json\JsonResource;

class GeneralSettingsResource extends JsonResource
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
            'main_office_title' => $this->main_office_title,
            'main_office_address' => $this->main_office_address,
            'main_office_phone' => $this->main_office_phone,
            'main_office_email' => $this->main_office_email,
            'main_office_lat' => $this->main_office_lat,
            'main_office_long' => $this->main_office_long,
            'phone' => $this->phone,
            'offer_url' => $this->offer_url,
            'consent_url' => $this->consent_url,
            'refill_account_acquiring' => $this->refill_account_acquiring,
            'claim_payment_acquiring' => $this->claim_payment_acquiring,
            'support_topics' => SupportTopics::withoutGlobalScope('published')
                ->where(['is_published' => true])
                ->get(['id', 'name'])
        ];
    }
}
