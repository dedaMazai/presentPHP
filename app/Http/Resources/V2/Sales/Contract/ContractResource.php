<?php

namespace App\Http\Resources\V2\Sales\Contract;

use App\Http\Resources\V2\Sales\ArticleOrderCollection;
use App\Http\Resources\V2\Sales\PropertyResource;
use App\Models\V2\Contract\Contract;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractResource extends JsonResource
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
        /** @var Contract $this */
        $stepName = '';
        if($this->getStepName() == 'Подготовка пакета документов' || $this->getStepName() == 'Подписание договора') {
            $stepName = 'Оформление сделки';
        } elseif ($this->getStepName() == 'Оплата') {
            $stepName = 'Оплата';
        } elseif ($this->getStepName() == 'Регистрация') {
            $stepName = 'Регистрация';
        } elseif ($this->getStepName() == 'Сверка БТИ и выдача ключей') {
            $stepName = 'Приемка';
        }


        switch ($this->getProperty()->getArticleStatusReception()) {
            case null:
            case 8:
            case 9:
            case 10:
            case 27:
            case 28:
            case 29:
                break;
            case 2:
            case 3:
            case 4:
            case 5:
                $stepName = 'Приемка';
                break;
            case 6:
            case 7:
            case 11:
                $stepName = 'Приемка завершена';
                break;
        }

        return [
            'demand_id' => null,
            'quantity' => $this->getArticleOrders()[0]->getQuantity(),
            'total_price' => $this->getEstimated(),
            'contract_info' => $this->getContractInfo(),
            'demand_number' => null,
            'contract_number' => $this->getContractInfo()['name'] ?? null,
            'is_booking_cancel_available' => false,
            'modified_date' => $this->getModifiedOn()?->format('Y.m.d H:i'),
            'object_price' => null,
            'sales_scheme' => $this->getService(),
            'stage' => $stepName,
            'property' => $this->getProperty() ? new PropertyResource($this->getProperty()) : null,
            'add_contracts_count' => $this->getContractsCount(),
            "customer_type" => !empty($this->getJointOwners()) ? $this->getJointOwners()[0]?->getCustomerType(): null,
        ];
    }
}
