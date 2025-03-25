<?php

namespace App\Http\Resources\Contact;

use App\Http\Resources\CityResource;
use App\Http\Resources\ImageResource;
use App\Models\Contact\ContactType;
use App\Models\Settings;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = [
            'title' => $this->title,
            'type' => $this->type->value,
            'icon_image' => (new ImageResource($this->iconImage)),
        ];

        if ($this->contactable instanceof Settings) {
            $data['city'] = $this->city ? (new CityResource($this->city)) : null;
        }

        if ($this->type->equals(ContactType::phone())) {
            $data['phone'] = $this->phone;
        } elseif ($this->type->equals(ContactType::email())) {
            $data['email'] = $this->email;
        } elseif ($this->type->equals(ContactType::map())) {
            $data['lat'] = $this->lat;
            $data['long'] = $this->long;
        }

        return $data;
    }
}
