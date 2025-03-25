<?php

namespace App\Http\Resources;

use App\Models\Receipt\ReceiptStatus;
use Illuminate\Http\Resources\Json\JsonResource;

class ReceiptResource extends JsonResource
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
            'year' => $this->getYear(),
            'month' => $this->getMonth(),
            'total' => $this->getTotal(),
            'pdf' => $this->getPdf(),
            'status' => $this->getStatus()->value,
            'paid' => $this->getPaid(),
            'is_paid' => $this->getStatus()->equals(ReceiptStatus::paid()),
        ];
    }
}
