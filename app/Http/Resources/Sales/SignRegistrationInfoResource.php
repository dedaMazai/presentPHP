<?php

namespace App\Http\Resources\Sales;

use App\Models\Sales\Customer\Customer;
use App\Models\V2\Contract\Contract;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class SignRegistrationInfoResource extends JsonResource
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
        if ($this->getRegistrationStage() == 'Договор зарегистрирован' && $this->getRegistrationDate() && $this->getService()?->value == '020020') {
            $signAndRegistrationResultInfo = 'Ожидайте, когда Пионер получит разрешение на строительство и с вами будет заключен ДДУ';
        } else {
            $signAndRegistrationResultInfo = null;
        }

        $sdSignatureIsExistApp = false;

        if (Carbon::parse($this->getDateOfSigningFact()) < Carbon::now()) {
            $sdSignatureIsExistApp = true;
        }

        if ($this->getService()?->value == '020020' || $this->getService()?->value == '020030') {
            $stages[] = [
                'number' => 1,
                'name' => 'Подписание договора',
                'description' => 'Выполните подписание договора.',
                'is_date_available' => false,
                'status' => 'closed',
                'date_title' => null,
            ];
        } else {
            $stages = [
                [
                    'number' => 1,
                    'name' => 'Подписание договора',
                    'description' => 'Выполните подписание договора.',
                    'is_date_available' => false,
                    'status' => 'closed',
                    'date_title' => null,
                ],
                [
                    'number' => 2,
                    'name' => 'Подготовка к регистрации',
                    'description' => 'Договор передан на регистрацию. Мы уведомим вас об окончании этого процесса.',
                    'date' => $this->getReceiptData()?->format('d.m.Y'),
                    'is_date_available' => true,
                    'status' => 'closed',
                    'date_title' => 'Плановая дата регистрации',
                ],
                [
                    'number' => 3,
                    'name' => 'Регистрация',
                    'description' => 'Договор успешно зарегистрирован в Росреестре.',
                    'is_date_available' => false,
                    'status' => 'closed',
                    'date_title' => null,
                ],
                [
                    'number' => 4,
                    'name' => 'Оплата',
                    'description' => 'Денежные средства поступили на счет застройщика. Следите за ходом строительства на сайте.',
                    'is_date_available' => false,
                    'status' => 'closed',
                    'date_title' => null,
                ]
            ];
        }

        $statusDone = false;
        foreach ($this->getPaymentPlans() as $paymentPlan) {
            if ($paymentPlan->getSumPayment() != null) {
                $statusDone = true;
            }
        }

        $filteredJointOwners = collect($this->getJointOwners())->filter(function ($jointOwner) {
            /** @var Customer $jointOwner */
            return $jointOwner->getRole()->value === 1 && $jointOwner->getSignStatus()?->value != 47;
        });
        $jOwnerClosed = $filteredJointOwners->count() != 0;

        $filteredJointOwners = collect($this->getJointOwners())->filter(function ($jointOwner) {
            /** @var Customer $jointOwner */
            return $jointOwner->getRole()?->value == 1 && $jointOwner->getSignStatus()?->value == 47;
        });
        $jOwnerDone = $filteredJointOwners->count() != 0;

        $filteredJointOwners = collect($this->getJointOwners())->filter(function ($jointOwner) {
            /** @var Customer $jointOwner */
            return $jointOwner->getRole()?->value == 1 && $jointOwner->getSdSignatureIsExistApp() == true && $jointOwner->getSdSignatureIsExistDoc() == true;
        });
        $jOwnerSignApp = $filteredJointOwners->count() != 0;

        if ($jOwnerClosed) {
            $stages[0]['status'] = 'closed';
            $stages[1]['status'] = 'closed';
            $stages[2]['status'] = 'closed';
            $stages[3]['status'] = 'closed';
        } elseif ($jOwnerDone || ($this->getRegistrationStage() == 'Подготовка к регистрации' || $this->getStepName() == 'Подписание договора')) {
            $stages[0]['status'] = 'active';
            $stages[1]['status'] = 'closed';
            $stages[2]['status'] = 'closed';
            $stages[3]['status'] = 'closed';
        }  elseif ($this->getStepName() == 'Оплата') {
            $stages[0]['status'] = 'done';
            $stages[1]['status'] = 'closed';
            $stages[2]['status'] = 'closed';
            $stages[3]['status'] = 'closed';
        } elseif ($this->getRegistrationStage() == 'На регистрации' || $this->getRegistrationStage() == 'Документы отправлены на Регистрацию') {
            $stages[0]['status'] = 'done';
            $stages[1]['status'] = 'active';
            $stages[2]['status'] = 'closed';
            $stages[3]['status'] = 'closed';
        } elseif (($this->getRegistrationDate() != null && $this->getRegistrationNumber() != null) && $this->getRegistrationStage() == 'Договор зарегистрирован') {
            $stages[0]['status'] = 'done';
            $stages[1]['status'] = 'done';
            $stages[2]['status'] = 'done';
            $stages[3]['status'] = 'active';
        }  elseif (($this->getRegistrationDate() != null && $this->getRegistrationNumber() != null) || $this->getRegistrationStage() == 'Договор зарегистрирован') {
            $stages[0]['status'] = 'done';
            $stages[1]['status'] = 'done';
            $stages[2]['status'] = 'active';
            $stages[3]['status'] = 'closed';
        } elseif ($statusDone) {
            $stages[0]['status'] = 'done';
            $stages[1]['status'] = 'done';
            $stages[2]['status'] = 'done';
            $stages[3]['status'] = 'done';
        }

        $sdSignatureIsExistApp = false;

        if (Carbon::parse($this->getDateOfSigningFact()) < Carbon::now()) {
            $filteredJointOwners = collect($this->getJointOwners())->filter(function ($jointOwner) {
                /** @var Customer $jointOwner */
                return $jointOwner->getSdSignatureIsExistDoc() && $jointOwner->getSdSignatureIsExistApp();
            });

            if ($filteredJointOwners->count() == count($this->getJointOwners())) {
                $sdSignatureIsExistApp = true;
            }
        }

        if ($this->getElectroReg() == 'yes') {
            $regInfo = [];
        } elseif ($this->getElectroReg() == 'no') {
            $regInfo = [];
        } elseif ($this->getElectroReg() == 'sber') {
            $regInfo[] = 'Подписание происходит электронно. Вам позвонит менеджер и попросит в момент звонка ввести sms-код в мобильном приложении.';
            $regInfo[] = 'Регистрация договора будет проходить через платформу электронной регистрации Сбербанка ДомКлик. По факту регистрации договора на почту, указанную в профиле дольщика, придет письмо от ДомКлик с архивом документов (zip)';
        } elseif ($this->getElectroReg() == 'tinkoff') {
            $regInfo[] = 'Регистрация договора будет проходить через платформу электронной регистрации банка Тинькофф. По факту регистрации договора на почту, указанную в профиле дольщика, придет письмо с архивом документов (zip)';
        }

        $receiptDate = null;

        if ($this->getRegistrationDate() != null && $stages[1]['status'] == 'closed') {
            foreach ($stages as $key => $stage) {
                $stages[$key]['date'] = $this->getReceiptData()?->format('d.m.Y');
            }
        }

        return [
            'contract_number' => $this->getName(),
            'contract_date' => $this->getDate()?->format('d.m.Y'),
            'receipt_datе' => $this->getReceiptData()?->format('d.m.Y'),
            'date_of_signing_fact' => $this->getDateOfSigningFact()?->format('d.m.Y H:i'),
            'registration_date' => $this->getRegistrationDate()?->format('d.m.Y'),
            'registration_number' => $this->getRegistrationNumber(),
            'sign_and_registration_stages' => new SignAndRegistrationStageCollection($stages),
            'register_type' => $this->getElectroReg(),
            'register_info' => $regInfo,
            'sign_and_registration_result_info' => $signAndRegistrationResultInfo,
            'is_everyone_sign' => $sdSignatureIsExistApp,
            'joint_owners_sign_info' => new JointOwnerSignInfoCollection($this->getJointOwners()),
        ];
    }
}
