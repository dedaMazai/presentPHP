<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\JsonResource;

class FavoritePropertyResource extends JsonResource
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
            'code' => $this->getCode(),
            'type' => $this->getType()?[
                'code' => $this->getType()->value,
                'name' => $this->getType()->label,
            ]:null,
            'variant' => $this->getVariant()?[
                'code' => $this->getVariant()->value,
                'name' => $this->getVariant()->label,
            ]:null,
            'status' => $this->getStatus()?[
                'code' => $this->getStatus()->value,
                'name' => $this->getStatus()->label,
            ]:null,
            'number' => $this->getNumber(),
            'floor' => $this->getFloor(),
            'address_name' => $this->getAddress()->getName(),
            'layout_number' => $this->getLayoutNumber(),
            'rooms' => $this->getRooms(),
            'quantity' => $this->getQuantity(),
            'price' => $this->getPrice(),
            'plan' => $this->getPlan()?->getUrl(),
            'url' => $this->getUrl(),
            'address_post' => $this->getAddressPost(),
            'price_discount' => $this->getPriceDiscount(),
            'plan_object' => $this->getPlanObject(),
            'free_booking_period' => $this->getFreeBookingPeriod()?[
                'type' => $this->getFreeBookingPeriod()['type']?->label,
                'duration' => $this->getFreeBookingPeriod()['duration']
            ]:null,
            'name_lk' => $this->getNameLk(),
            'is_booking_availible' => $this->getIsBookingAvailible(),
        ];
    }
}
