<?php

namespace App\Http\Resources\LegalPerson;

use App\Services\LegalPerson\Dto\LegalPersonDto;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->type_message != 1) {
            return [
                'account' => null,
                'account_type' => null,
                'type_message' => intval($this->type_message),
                'message' => $this->message ?? null,
            ];
        } else {

            /** @var LegalPersonDto $this */
            return [
                'account' => new CheckInnResource($this),
                'account_type' => $this->account_type,
                'type_message' => intval($this->type_message),
                'message' => $this->message,
            ];
        }
    }
}
