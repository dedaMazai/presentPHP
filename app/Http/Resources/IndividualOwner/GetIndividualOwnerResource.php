<?php

namespace App\Http\Resources\IndividualOwner;

use App\Http\Resources\JointOwnerDocumentCollection;
use App\Http\Resources\V2\UserDocumentResource;
use Illuminate\Http\Resources\Json\JsonResource;

class GetIndividualOwnerResource extends JsonResource
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
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'middle_name' => $this->middleName,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'email' => $this->email,
            'inn' => $this->inn,
            'snils' => $this->snils,
            'birth_date' => $this->birthDate,
            'married' => $this->married,
            'is_rus' => $this->is_rus,
            'document' => collect($this->documents)
                    ->values()
                    ->map(function ($document) {
                        return new UserDocumentResource($document);
                    }) ?? null,
        ];
    }
}
