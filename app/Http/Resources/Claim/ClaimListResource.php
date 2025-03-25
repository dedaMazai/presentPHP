<?php

namespace App\Http\Resources\Claim;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ClaimListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->getId(),
            'theme' => $this->getTheme()->value,
            'status' => $this->getStatus()->value,
            'created_at' => $this->getCreatedAt()->toDateTimeString(),
            'closed_at' => $this->getClosedOn()?->toDateTimeString() ?? $this->getClosedAt(),
            'payment_status' => $this->getPaymentStatus()?->value,
            'arrival_date' => $this->getArrivalDate()?->toDateTimeString(),
            'pass_type' => $this->getPassType()?->value,
            'total_payment' => $this->getTotalPayment(),
            'claim_number' => (string)$this->getClaimNumber(),
            'is_not_read_sms' => $this->getIsNotReadSMS(),
            'is_not_read_document' => $this->getIsNotReadDocument(),
            'pass_car' => new ClaimPassCarResource($this->getPassCar()),
            'pass_human' => new ClaimPassHomanResource($this->getPassHuman()),
            'scheduled_start' => $this->getScheduledStart()?->toDateTimeString(),
            'scheduled_end' => $this->getScheduledEnd()?->toDateTimeString(),
            'services' => new SimpleClaimServiceCollection($this->getServices())
        ];
    }
}
