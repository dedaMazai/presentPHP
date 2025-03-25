<?php

namespace App\Http\Resources\Sales;

use App\Models\Sales\Customer\Customer;
use App\Models\V2\Contract\Contract;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class AddSignRegistrationInfoResource extends JsonResource
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
        $sdSignatureIsExistApp = false;

        if ($this->getDateOfSigningFact() != null && Carbon::parse($this->getDateOfSigningFact()) < Carbon::now()) {
            $sdSignatureIsExistApp = true;
        }

        $stages = [
            [
                'number' => 1,
                'name' => 'Подписание договора',
                'description' => 'Выполните подписание соглашения.',
                'is_date_available' => false,
                'status' => 'closed',
                'date_title' => null,
            ],
            [
                'number' => 2,
                'name' => 'Регистрация',
                'description' => 'Соглашение передано на регистрацию. Мы уведомим вас об окончании этого процесса.',
                'is_date_available' => true,
                'status' => 'closed',
                'date_title' => 'Плановая дата регистрации',
            ],
            [
                'number' => 3,
                'name' => 'Соглашение зарегистрировано',
                'description' => 'Соглашение успешно зарегистрировано в Росреестре.',
                'is_date_available' => false,
                'status' => 'closed',
                'date_title' => null,
            ],
        ];

        $statusDone = false;
        foreach ($this->getPaymentPlans() as $paymentPlan) {
            if ($paymentPlan->getSumPayment() != null) {
                $statusDone = true;
            }
        }

        $filteredJointOwners = collect($this->getJointOwners())->filter(function ($jointOwner) {
            /** @var Customer $jointOwner */
            return $jointOwner->getRole()->value === 1;
        });
        $countOfJointOwners = $filteredJointOwners->count();

        $filteredJointOwnersWithoutCode = $filteredJointOwners->filter(function ($jointOwner) {
            /** @var Customer $jointOwner */
            return $jointOwner->getSignStatus() != 47;
        });
        $countOfJointOwnersWithoutCode = $filteredJointOwners->count();

        $filteredJointOwnersWithCode = $filteredJointOwners->filter(function ($jointOwner) {
            /** @var Customer $jointOwner */
            return $jointOwner->getSignStatus() == 47 && $jointOwner->getEsValidityDate() > Carbon::now();
        });
        $countOfJointOwnersWithCode = $filteredJointOwners->count();

        if ($countOfJointOwnersWithoutCode != 0) {
            $stages[0]['status'] = 'closed';
            $stages[1]['status'] = 'closed';
            $stages[2]['status'] = 'closed';
        } elseif ($countOfJointOwners == $countOfJointOwnersWithCode && $countOfJointOwners != 0) {
            $stages[0]['status'] = 'active';
            $stages[1]['status'] = 'closed';
            $stages[2]['status'] = 'closed';
        } elseif ($this->getRegistrationStage() == 'Подготовка к регистрации') {
            $stages[0]['status'] = 'done';
            $stages[1]['status'] = 'closed';
            $stages[2]['status'] = 'closed';
        } elseif ($this->getRegistrationStage() == 'На регистрации' || $this->getRegistrationStage() == 'Документы отправлены на Регистрацию') {
            $stages[0]['status'] = 'done';
            $stages[1]['status'] = 'active';
            $stages[2]['status'] = 'closed';
        } elseif ($this->getRegistrationDate() != null && $this->getRegistrationNumber() != null) {
            $stages[0]['status'] = 'done';
            $stages[1]['status'] = 'done';
            $stages[2]['status'] = 'done';
        }

        if ($stages[1]['status'] != 'closed' && $this->getReceiptData() != null) {
            $stages[1]['date'] = $this->getReceiptData()->format('d.m.Y');
        }


        return [
            'additional_contract_number' => $this->getName(),
            'additional_contract_contract_date' => $this->getDate()?->format('d.m.Y'),
            'receipt_datе' => $this->getReceiptData()?->format('d.m.Y'),
            'date_of_signing_fact' => $this->getDateOfSigningFact()?->format('d.m.Y H:i'),
            'registration_date' => $this->getRegistrationDate()?->format('d.m.Y'),
            'registration_number' => $this->getRegistrationNumber(),
            'sign_and_registration_stages' => new SignAndRegistrationStageCollection($stages),
            'is_electro_reg' => !($this->getElectroReg() == 'no'),
            'electro_reg_info' => $this->getElectroRegInfo(),
            'is_everyone_sign' => $sdSignatureIsExistApp,
            'joint_owners_sign_info' => new JointOwnerSignInfoCollection($this->getJointOwners()),
        ];
    }
}
