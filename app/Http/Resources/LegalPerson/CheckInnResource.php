<?php

namespace App\Http\Resources\LegalPerson;

use App\Services\LegalPerson\Dto\LegalPersonDto;
use Illuminate\Http\Resources\Json\JsonResource;

class CheckInnResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var LegalPersonDto $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'inn' => $this->inn,
            'ogrn' => $this->ogrn,
            'kpp' => $this->kpp,
            'address_legal' => $this->address_legal,
            'phone' => $this->phone,
            'mail' => $this->mail,
        ];
    }
}
