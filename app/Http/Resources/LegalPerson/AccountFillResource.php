<?php

namespace App\Http\Resources\LegalPerson;

use App\Services\LegalPerson\Dto\LegalPersonDto;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountFillResource extends JsonResource
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
            'account' => new CheckInnResource($this),
            'account_type' => $this->account_type,
        ];
    }
}
