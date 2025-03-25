<?php

namespace App\Http\Resources\Sales\Demand;

use App\Http\Resources\Sales\PropertyResource;
use Illuminate\Http\Resources\Json\JsonResource;

class DemandResource extends JsonResource
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
            'is_booking_paid' => $this->getBookingPaymentStatus()?->isPaid(),
            'begin_date' => $this->getBeginDate()?->toDateTimeString(),
            'end_date' => $this->getEndDate()?->toDateTimeString(),
            'status' => $this->getStatus()?->value,
            'state' => $this->getState()?->value,
            'quantity' => $this->getMainArticleOrder()?->getQuantity(),
            'sum' => $this->getMainArticleOrder()?->getSum(),
            'name' => $this->getMainArticleOrder()?->getName(),
            'paid_booking_sum' => $this->getPaidBookingContract()?->getEstimated(),
            'paid_booking_period_days' => $this->getBeginDate()?->diffInDays($this->getEndDate()),
            'paid_booking_payment_end_at' => !$this->getBookingPaymentStatus()?->isPaid() ?
                $this->getBeginDate()?->addRealHour()->toDateTimeString() : null,
            'property' => $this->getProperty() ? new PropertyResource($this->getProperty()) : null
        ];
    }
}
