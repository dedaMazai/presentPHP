<?php

namespace App\Http\Resources\V2\Sales\Contract\JointOwner;

use App\Http\Resources\Contract\JointOwner\SignInfoResource;
use App\Http\Resources\Contract\JointOwner\SignStatementDocumentResource;
use App\Http\Resources\Meter\MeterEnterPeriodResource;
use App\Services\Utils\DateFormatter;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class JointOwnerListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if (isset($this['primaryContact']['code'])) {
            $age = Carbon::parse($this['customer']->getBirthDate());
            $age = DateFormatter::birthDateFormatter($age);
            $customer = $this['customer'];
            $fullName = trim($customer->getLastName() . ' ' . ($customer->getFirstName() ?? null ) . ' ' . ($customer->getMiddleName() ?? null));
            $id = $this['primaryContact']['code'];
        } else {
            $age = Carbon::parse($this['birthDate'])->age;
            $fullName = trim($this['lastName'] . ' ' . ($this['firstName'] ?? null ) . ' ' . ($this['middleName'] ?? null));
            $id = $this['contactId'];
        }

        $isRus = true;
        $docForCourier = [];

        if ($age > 18) {
            $ageCategory = 'adult';
            if ($isRus == true) {
                $docForCourier = ['Паспорт', 'СНИЛС'];
            } else {
                $docForCourier = ['Паспорт', 'Нотариально заверенный перевод паспорт', 'Подтверждение регистрации', 'СНИЛС'];
            }
        } elseif ($age > 14 && $age < 18) {
            $ageCategory = 'teen';
            if ($isRus == true) {
                $docForCourier = ['Паспорт', 'Свидетельство о рождении', 'СНИЛС'];
            } else {
                $docForCourier = ['Паспорт', 'Нотариально заверенный перевод паспорт', 'Свидетельство о рождении', 'Подтверждение регистрации', 'СНИЛС'];
            }
        } elseif ($age < 14) {
            $ageCategory = 'child';
            if ($isRus == true) {
                $docForCourier = ['Свидетельство о рождении'];
            } else {
                $docForCourier = ['Нотариально заверенный перевод паспорт', 'Подтверждение регистрации', 'Свидетельство о рождении'];
            }
        }

        $courierAddress = false;

        if ($this['customer']->getSignStatus()) {
            if ($this['customer']->getSignStatus() ||
                $this['customer']->getSignStatus()->value == 41 &&
                $this['addressCourier'] == null) {
                $courierAddressForModify = true;
            }
        }

        return [
            'id' => $id ?? null,
            'joint_owner_id' => $this['id'] ?? null,
            'full_name' => $fullName,
            'age_category' => $ageCategory ?? null,
            'is_rus' => $isRus,
            'city_courier' => $this['cityCourier'] ?? null,
            'address_courier' => $this['addressCourier'] ?? null,
            'address_description' => $this['description'] ?? null,
            'is_address_courier_available_for_modify' => $courierAddressForModify ?? null,
            'sign_info' => new SignInfoResource($this['customer']) ?? null,
            'required_documents_for_courier' => $docForCourier,
            'sign_statement_document' => new SignStatementDocumentResource($this['document']) ?? null,
        ];
    }
}
