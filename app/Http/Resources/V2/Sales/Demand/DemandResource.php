<?php

namespace App\Http\Resources\V2\Sales\Demand;

use App\Http\Resources\V2\Sales\PropertyResource;
use App\Models\V2\Sales\Demand\Demand;
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
        // phpcs:disable
        /** @var Demand $this */
        $stepName = '';
        if($this->getStepName() == 'Сбор информации') {
            $stepName = 'Указание условий сделки';
        } elseif ($this->getStepName() == 'Сбор информации' && $this->getStatus()->value == 32) {
            $stepName = 'Ожидайте подготовки договора';
        }

        return [
            'demand_id' => $this->getId(), //
            'quantity' => $this->getMainArticleOrder()?->getQuantity(), //
            'total_price' => $this->getMainArticleOrder()?->getSum(), //
            'contract_info' => null, //
            'demand_number' => (int)$this->getNumber(), //
            'contract_number' => null,
            'is_booking_cancel_available' => $this->getStatus()->value == 8, //
            'modified_date' => $this->getModifiedOn(), //
            'object_price' =>  intval(in_array($this->getArticlePrice(), $this->getArticleOrders())?null:$this->getArticlePrice()), //
            'sales_scheme' => new SalesSchemeResource($this->getSalesScheme()), //
            'payment_booking' => $this->getPaymentBooking() != [] ? $this->getPaymentBooking() : null, //
            'stage' => $stepName, //
            'property' => $this->getProperty() ? new PropertyResource($this->getProperty()) : null //
        ];
    }
}
