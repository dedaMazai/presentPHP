<?php

namespace App\Http\Resources\V3\Sales\Demand;

use App\Http\Resources\V2\Sales\Demand\SalesSchemeResource;
use App\Http\Resources\V2\Sales\PropertyResource;
use App\Models\V2\Sales\Demand\Demand;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class DemandResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        // phpcs:disable
        /** @var Demand $this */
        $stepName = "";

        if ($this->getStepName() === "Сбор информации") {
            $stepName = $this->getStatus()->value === 32 ? "Ожидайте подготовки договора" : "Указание условий сделки";
        }

        return [
            "demand_id" => $this->getId(),
            "quantity" => $this->getMainArticleOrder()?->getQuantity(),
            "total_price" => $this->getMainArticleOrder()?->getSum(),
            "contract_info" => null,
            "demand_number" => (int)$this->getNumber(),
            "contract_number" => null,
            "is_booking_cancel_available" => $this->getStatus()->value === 8,
            "modified_date" => $this->getModifiedOn(),
            "object_price" =>  in_array($this->getArticlePrice(), $this->getArticleOrders()) ? null : $this->getArticlePrice(),
            "sales_scheme" => new SalesSchemeResource($this->getSalesScheme()),
            "payment_booking" => !empty($this->getPaymentBooking()) ?: null,
            "stage" => $stepName,
            "property" => $this->getProperty() ? new PropertyResource($this->getProperty()) : null,
            "customer_type" => $this->getCustomer()?->getCustomerType(),
        ];
    }
}
