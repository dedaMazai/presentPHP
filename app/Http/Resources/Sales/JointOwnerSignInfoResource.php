<?php

namespace App\Http\Resources\Sales;

use App\Models\Sales\Customer\Customer;
use Illuminate\Http\Resources\Json\JsonResource;

class JointOwnerSignInfoResource extends JsonResource
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
        return [
            'full_name' => $this->getLastName() . ' ' . $this->getFirstName() . ' ' . $this->getMiddleName(),
            'is_doc_sign' => $this->getSdSignatureIsExistDoc() == null ? false : $this->getSdSignatureIsExistDoc(),
            'is_app_sign' => $this->getSdSignatureIsExistApp() == null ? false : $this->getSdSignatureIsExistApp(),
        ];
    }
}
