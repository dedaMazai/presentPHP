<?php

namespace App\Http\Resources\V2\Sales;

use App\Models\V2\Sales\LetterOfCreditBank;
use Illuminate\Http\Resources\Json\JsonResource;

class LetterOfCreditBanksResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var LetterOfCreditBank $this */
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'image' => $this->getImage(),
        ];
    }
}
