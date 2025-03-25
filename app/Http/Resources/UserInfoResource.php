<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'last_name' => $this->getLastName(),
            'first_name' => $this->getFirstName(),
            'middle_name' => $this->getMiddleName(),
            'gender' => $this->getGender(),
            'phone' => $this->getPhone(),
            'email' => $this->getEmail(),
            'birth_date' => $this->getBirthDate(),
            'inn' => $this->getInn(),
            'snils' => $this->getSnils(),
            'married' => $this->getMarried(),
            'is_rus' => $this->isRus($this->getDocumentType()),
        ];
    }

    private function isRus(string $isRus)
    {
        switch ($isRus) {
            case "4":
                return false;
            case "1":
                return true;
            default:
                return null;
        }
    }
}
