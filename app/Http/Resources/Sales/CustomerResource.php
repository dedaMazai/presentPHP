<?php

namespace App\Http\Resources\Sales;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'id' => $this->getId(),
            'joint_owner_id' => $this->getJointOwnerId(),
            'last_name' => $this->getLastName(),
            'first_name' => $this->getFirstName(),
            'middle_name' => $this->getMiddleName(),
            'gender' => $this->getGender()?->value,
            'phone' => $this->getPhone(),
            'phone_home' => $this->getPhoneHome(),
            'email' => $this->getEmail(),
            'birth_date' => $this->getBirthDate()->toDateString(),
            'birth_place' => $this->getBirthPlace(),
            'document_type' => $this->getDocumentType()?->value,
            'document_series' => $this->getDocumentSeries(),
            'document_number' => $this->getDocumentNumber(),
            'document_give_out' => $this->getDocumentGiveOut(),
            'document_date' => $this->getDocumentDate()?->toDateString(),
            'subdivision_code' => $this->getSubdivisionCode(),
            'address_registration' => $this->getAddressRegistration(),
            'citizenship' => $this->getCitizenship(),
            'inn' => $this->getInn(),
            'snils' => $this->getSnils(),
            'part' => $this->getPart(),
            'es_validity_date' => $this->getEsValidityDate()?->toDateString(),
            'sign_status' => $this->getSignStatus()?->value,
            'sd_signature_doc_page_url' => $this->getSdSignatureDocPageUrl(),
            'sd_signature_app_page_url' => $this->getSdSignatureAppPageUrl(),
            'sd_signature_is_exist_doc' => $this->getSdSignatureIsExistDoc(),
            'sd_signature_is_exist_app' => $this->getSdSignatureIsExistApp(),
            'owner_type' => $this->getOwnerType()?->value,
            'owner_type_comment' => $this->getOwnerTypeComment(),
            'uin' => $this->getUin(),
            'role' => $this->getRole()?->value,
        ];
    }
}
