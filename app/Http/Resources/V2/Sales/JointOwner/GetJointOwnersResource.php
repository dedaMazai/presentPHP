<?php

namespace App\Http\Resources\V2\Sales\JointOwner;

use App\Services\Utils\AgeFormatter;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GetJointOwnersResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if (method_exists($this->resource, 'getBirthDate')) {
            if (!empty($this->resource->getBirthDate())) {
                $age = Carbon::parse($this->resource->getBirthDate())->age;
                $ageCategory = AgeFormatter::getAgeCategory($age);
            }
        }

        if (method_exists($this->resource, 'getLegalEntityData')) {
            $legalEntityData = $this->resource->getLegalEntityData();
        } else {
            $legalEntityData = $this->legalEntityData ?? null;
        }

        if (method_exists($this->resource, 'getSignatory')) {
            $signatory = $this->resource->getSignatory();
        } else {
            $signatory = $this->signatory ?? null;
        }

        // Если есть legalEntityData – это юрлицо
        if ($legalEntityData) {
            // Собираем объект юрлица
            $legal = [
                'account_id'     => $legalEntityData->getAccountId(),
                'joint_owner_id' => $legalEntityData->getJointOwnerId(),
                'account_type'   => $legalEntityData->getAccountType(),
                'name'           => $legalEntityData->getName(),
                'inn'            => $legalEntityData->getInn(),
            ];

            // Если ещё и подписант (signatory) есть – вернём массив из двух объектов
            if ($signatory) {
                return [
                    $legal,
                    [
                        'id'        => $signatory->getId(),
                        'full_name' => $signatory->getFullName(),
                        'label'     => $signatory->getLabel(),
                        'signatory' => $signatory->getSignatory(),
                    ],
                ];
            }

            // Юрлицо без подписанта – один объект в массиве
            return [$legal];
        }

        // Иначе физлицо
        return [
            'id' => $this->id ?? (method_exists($this->resource, 'getId') ? $this->resource->getId() : null),
            'joint_owner_id' => $this->jointOwnerId ?? (method_exists($this->resource, 'getJointOwnerId') ? $this->resource->getJointOwnerId() : null),
            'full_name' => $this->fullName ?? (method_exists($this->resource, 'getFullName') ? $this->resource->getFullName() : null),
            'part' => $this->part ?? (method_exists($this->resource, 'getPart') ? $this->resource->getPart() : null),
            'type' => $this->type ?? (method_exists($this->resource, 'getOwnerType') ? $this->resource->getOwnerType() : null),
            'age_category' => $this->ageCategory ?? ($ageCategory ?? null),
            'is_all_profile_data' => $this->isAllProfileData ?? false,
            'label' => $this->label ?? (method_exists($this->resource, 'getLabel') ? $this->resource->getLabel() : null),
        ];
    }
}
