<?php

namespace App\Http\Resources\Claim;

use Illuminate\Http\Resources\Json\JsonResource;

class DetailClaimResource extends JsonResource
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
            'id' => $this['claim']->getId(),
            'claim_number' => $this['claim']->getNumber(),
            'theme' => $this['claim']->getTheme()->value,
            'status' => $this['claim']->getStatus()->value,
            'created_at' => $this['claim']->getCreatedAt()->toDateTimeString(),
            'closed_at' => $this['claim']->getClosedAt()?->toDateTimeString(),
            'payment_status' => $this['claim']->getPaymentStatus()?->value,
            'comment' => $this['claim']->getComment(),
            'arrival_date' => $this['claim']->getArrivalDate()?->toDateTimeString(),
            'payment_date' => $this['claim']->getPaymentDate()?->toDateTimeString(),
            'total_payment' => $this['claim']->getTotalPayment(),
            'scheduled_start' => $this['claim']->getScheduledStart()?->toDateTimeString(),
            'scheduled_end' => $this['claim']->getScheduledEnd()?->toDateTimeString(),
            'pass_type' => $this['claim']->getPassType()?->value,
            'pass_status' => $this['claim']->getPassStatus()?->value,
            'confirmation_code' => $this['claim']->getConfirmationCode(),
            'comment_quality' => $this['claim']->getCommentQuality(),
            'rating' => $this['claim']->getRating(),
            'is_not_read_sms' => $this['claim']->getIsNotReadSMS(),
            'is_not_read_document' => $this['claim']->getIsNotReadDocument(),
            'pdf' => $this['receipt']['url'] ?? null,
            'pass_car' => new ClaimPassCarResource($this['claim']->getPassCar()),
            'pass_human' => new ClaimPassHomanResource($this['claim']->getPassHuman()),
            'executors' => new ClaimExecutorCollection($this['claim']->getExecutors()),
            'services' => new ClaimServiceCollection($this['claim']->getServices()),
            'images' => new ClaimImageCollection($this['claim']->getImages())
        ];
    }
}
