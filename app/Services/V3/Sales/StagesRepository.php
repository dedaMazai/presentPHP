<?php

namespace App\Services\V3\Sales;

use App\Models\Finishing;
use App\Models\Sales\Ownership;
use App\Models\Sales\OwnerType;
use App\Models\Sales\StageStatus;
use App\Models\V2\Contract\Contract;
use App\Models\V2\Sales\IconNavbar;
use App\Models\V2\Sales\Property\Property;
use App\Models\V2\Sales\SaleStage;
use App\Models\V2\Sales\SaleSubstage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Class OwnershipRepository
 *
 * @package App\Services\Sales
 */
class StagesRepository
{
    public function makeStages(array $demand, array $characteristics = [], $property = null): array
    {
        // phpcs:disable
        $saleStages = [];

        $stages[0]['number'] = 1;
        $stages[1]['number'] = 2;
        $stages[2]['number'] = 3;
        $stages[3]['number'] = 4;
        $stages[4]['number'] = 5;
        $stages[5]['number'] = 6;
        $stages[6]['number'] = 7;
        $stages[0]['name'] = 'Условия сделки';
        $stages[1]['name'] = 'Оформление сделки';
        $stages[2]['name'] = 'Оплата';
        $stages[3]['name'] = 'Подписание и регистрация';
        $stages[4]['name'] = 'Дополнительные соглашения';
        $stages[5]['name'] = 'Приемка помещения';
        $stages[6]['name'] = 'Дорасчет по кадастру';

        $stages[4]['status'] = 'closed';
        $stages[5]['status'] = 'closed';
        $stages[6]['status'] = 'closed';

        $stages[4]['message'] = '';
        $stages[5]['message'] = '';
        $stages[6]['message'] = '';

        $stages[4]['code'] = 'add_agreement';
        $stages[5]['code'] = 'inspection';
        $stages[6]['code'] = 'bti';

        $stages[4]['icon'] = Storage::url('public/images/icons/stages/dop_soglasheniya_disabled.png');
        $stages[5]['icon'] = Storage::url('public/images/icons/stages/doraschet_disabled.png');
        $stages[6]['icon'] = Storage::url('public/images/icons/stages/priemka_disabled.png');

        $personalOwnerships = collect($demand['jointOwners'] ?? null)?->where('ownerType.code', '=', OwnerType::personal()->value);
        $sharedOwnerships = collect($demand['jointOwners'] ?? null)?->where('ownerType.code', '=', OwnerType::shared()->value);
        $jointOwnerships = collect($demand['jointOwners'] ?? null)?->where('ownerType.code', '=', OwnerType::joint()->value);

        /** @var Property $property */
        if ($property->getArticleVariantTm1Code() != 1) {
            $isFinishing = false;
        } else {
            $finishingSalesStart = isset($demand['finishingSalesStart'])?Carbon::parse($demand['finishingSalesStart']):null;
            $finishingSalesStop = isset($demand['finishingSalesStop'])?Carbon::parse($demand['finishingSalesStop']):null;

            if ($finishingSalesStart == null && $finishingSalesStop == null) {
                $isFinishing = false;
            } elseif (!Carbon::today()->betweenIncluded($finishingSalesStart, $finishingSalesStop)) {
                $isFinishing = false;
            } elseif (Carbon::today()->betweenIncluded($finishingSalesStart, $finishingSalesStop)) {
                $isFinishing = true;
            } elseif (Carbon::now() < $finishingSalesStart || Carbon::now() > $finishingSalesStart) {
                $isFinishing = false;
            } elseif (Carbon::now() > $finishingSalesStart || Carbon::now() < $finishingSalesStart) {
                $isFinishing = true;
            }
        }

        $isEscrow = $property->getIsEscrow();

        if (isset($demand['letterOfCreditBankId']) && $demand['letterOfCreditBankId'] != null) {
            $statusMessage[0] = $demand['paymentModeCode']['name'];
        } else {
            $statusMessage[0] = 'Необходимо указать';
        }

        if ($personalOwnerships->count() > 0) {
            $statusMessage[1] = 'Индивидуальная собственность';
        } elseif ($sharedOwnerships->count() > 0) {
            $statusMessage[1] = 'Долевая собственность';
        } elseif ($jointOwnerships->count() > 0) {
            $statusMessage[1] = 'Совместная собственность';
        } else {
            $statusMessage[1] = 'Необходимо указать';
        }

        if (isset($demand['baseFinishVariant'])) {
            $finishing = Finishing::where('finishing_id', '=', $demand['baseFinishVariant']['id'])->first();
            $statusMessage[2] = $finishing?->name;
        } else {
            $statusMessage[2] = 'Необходимо указать';
        }

        if (isset($demand['depositorFizId'])) {
            $statusMessage[3] = 'Депонент указан';
        } else {
            $statusMessage[3] = 'Необходимо указать';
        }

        if ($demand['stepName']??'' == 'Сбор информации') {
            $stages[0]['status'] = 'active';
            $stages[1]['status'] = 'closed';
            $stages[2]['status'] = 'closed';
            $stages[3]['status'] = 'closed';

            if ($demand['articleOrders'][0]['serviceCode'] == '020020') {
                $stages[0]['code'] = 'transaction_terms';
                $stages[0]['substages'][0]['code'] = 'form_of_payment';
                $stages[0]['substages'][0]['number'] = '1';
                $stages[0]['substages'][0]['name'] = 'Форма оплаты';
                $stages[0]['substages'][0]['status_message'] = $statusMessage[0];
                $stages[1]['code'] = 'deal_processing';
                $stages[2]['code'] = 'payment';
                $stages[3]['code'] = 'signing_and_registration';

                $stages[0]['substages'][0]['icon'] = Storage::url('public/images/icons/nav_bar/oplata_active.png');
                $stages[0]['substages'][0]['icon_navbar'] = new IconNavbar(
                    number: 1,
                    activeIcon: Storage::url('public/images/icons/nav_bar/oplata_active.png'),
                    disableIcon: Storage::url('public/images/icons/nav_bar/oplata_disabled.png')
                );
            } elseif ($demand['articleOrders'][0]['serviceCode'] == '020030') {
                $stages[0]['code'] = 'transaction_terms';
                $stages[0]['substages'][0]['number'] = '1';
                $stages[0]['substages'][0]['code'] = 'form_of_payment';
                $stages[0]['substages'][0]['name'] = 'Форма оплаты';
                $stages[0]['substages'][1]['number'] = '2';
                $stages[0]['substages'][1]['code'] = 'type_of_ownership';
                $stages[0]['substages'][1]['name'] = 'Форма собственности';
                if ($isFinishing) {
                    $stages[0]['substages'][2]['number'] = '3';
                    $stages[0]['substages'][2]['code'] = 'finishing';
                    $stages[0]['substages'][2]['name'] = 'Отделка';
                    $stages[0]['substages'][2]['icon'] = Storage::url('public/images/icons/nav_bar/otdelka_active.png');
                    $stages[0]['substages'][2]['status_message'] = $statusMessage[2];
                }
                $stages[1]['code'] = 'deal_processing';
                $stages[2]['code'] = 'payment';
                $stages[3]['code'] = 'signing_and_registration';
                $stages[0]['substages'][0]['icon'] = Storage::url('public/images/icons/nav_bar/oplata_active.png');
                $stages[0]['substages'][1]['icon'] = Storage::url('public/images/icons/nav_bar/forma_sobstvennosti_active.png');
                $stages[0]['substages'][0]['status_message'] = $statusMessage[0];
                $stages[0]['substages'][1]['status_message'] = $statusMessage[1];
                $stages[0]['substages'][0]['icon_navbar'] = new IconNavbar(
                    number: 1,
                    activeIcon: Storage::url('public/images/icons/nav_bar/oplata_active.png'),
                    disableIcon: Storage::url('public/images/icons/nav_bar/oplata_disabled.png')
                );
                $stages[0]['substages'][1]['icon_navbar'] = new IconNavbar(
                    number: 2,
                    activeIcon: Storage::url('public/images/icons/nav_bar/forma_sobstvennosti_active.png'),
                    disableIcon: Storage::url('public/images/icons/nav_bar/forma_sobstvennosti_disabled.png')
                );
                $stages[0]['substages'][2]['icon_navbar'] = new IconNavbar(
                    number: 3,
                    activeIcon: Storage::url('public/images/icons/nav_bar/otdelka_active.png'),
                    disableIcon: Storage::url('public/images/icons/nav_bar/otdelka_disabled.png')
                );
            } elseif ($demand['articleOrders'][0]['serviceCode'] == '020011') {
                $stages[0]['code'] = 'transaction_terms';
                $stages[0]['substages'][0]['number'] = '1';
                $stages[0]['substages'][0]['code'] = 'form_of_payment';
                $stages[0]['substages'][0]['name'] = 'Форма оплаты';
                $stages[0]['substages'][1]['number'] = '2';
                $stages[0]['substages'][1]['code'] = 'type_of_ownership';
                $stages[0]['substages'][1]['name'] = 'Форма собственности';
                if ($isFinishing) {
                    $stages[0]['substages'][2]['number'] = '3';
                    $stages[0]['substages'][2]['code'] = 'finishing';
                    $stages[0]['substages'][2]['name'] = 'Отделка';
                    $stages[0]['substages'][2]['icon'] = Storage::url('public/images/icons/nav_bar/otdelka_active.png');
                    $stages[0]['substages'][2]['status_message'] = $statusMessage[2];
                    $stages[0]['substages'][2]['icon_navbar'] = new IconNavbar(
                        number: 3,
                        activeIcon: Storage::url('public/images/icons/nav_bar/otdelka_active.png'),
                        disableIcon: Storage::url('public/images/icons/nav_bar/otdelka_disabled.png')
                    );
                }
                if ($isEscrow) {
                    $stages[0]['substages'][3]['number'] = '4';
                    $stages[0]['substages'][3]['code'] = 'deponent';
                    $stages[0]['substages'][3]['name'] = 'Реквизиты возвратного счета';
                    $stages[0]['substages'][3]['icon'] = Storage::url('public/images/icons/nav_bar/rekvizity_scheta_active.png');
                    $stages[0]['substages'][3]['status_message'] = $statusMessage[3];
                    $stages[0]['substages'][3]['icon_navbar'] = new IconNavbar(
                        number: 3,
                        activeIcon: Storage::url('public/images/icons/nav_bar/rekvizity_scheta_active.png'),
                        disableIcon: Storage::url('public/images/icons/nav_bar/rekvizity_scheta_disabled.png')
                    );
                }
                $stages[1]['code'] = 'deal_processing';
                $stages[2]['code'] = 'payment';
                $stages[3]['code'] = 'signing_and_registration';
                $stages[0]['substages'][0]['icon'] = Storage::url('public/images/icons/nav_bar/oplata_active.png');
                $stages[0]['substages'][1]['icon'] = Storage::url('public/images/icons/nav_bar/forma_sobstvennosti_active.png');
                $stages[0]['substages'][0]['status_message'] = $statusMessage[0];
                $stages[0]['substages'][1]['status_message'] = $statusMessage[1];

                $stages[0]['substages'][0]['icon_navbar'] = new IconNavbar(
                    number: 1,
                    activeIcon: Storage::url('public/images/icons/nav_bar/oplata_active.png'),
                    disableIcon: Storage::url('public/images/icons/nav_bar/oplata_disabled.png')
                );
                $stages[0]['substages'][1]['icon_navbar'] = new IconNavbar(
                    number: 2,
                    activeIcon: Storage::url('public/images/icons/nav_bar/forma_sobstvennosti_active.png'),
                    disableIcon: Storage::url('public/images/icons/nav_bar/forma_sobstvennosti_disabled.png')
                );
            } else {
                $stages[0]['code'] = 'transaction_terms';
                $stages[0]['substages'][0]['number'] = '1';
                $stages[0]['substages'][0]['code'] = 'form_of_payment';
                $stages[0]['substages'][0]['name'] = 'Форма оплаты';
                $stages[0]['substages'][1]['number'] = '2';
                $stages[0]['substages'][1]['code'] = 'type_of_ownership';
                $stages[0]['substages'][1]['name'] = 'Форма собственности';
                if ($isFinishing) {
                    $stages[0]['substages'][2]['number'] = '3';
                    $stages[0]['substages'][2]['code'] = 'finishing';
                    $stages[0]['substages'][2]['name'] = 'Отделка';
                    $stages[0]['substages'][2]['icon'] = Storage::url('public/images/icons/nav_bar/otdelka_active.png');
                    $stages[0]['substages'][2]['icon_navbar'] = new IconNavbar(
                        number: 3,
                        activeIcon: Storage::url('public/images/icons/nav_bar/otdelka_active.png'),
                        disableIcon: Storage::url('public/images/icons/nav_bar/otdelka_disabled.png')
                    );
                    $stages[0]['substages'][2]['status_message'] = $statusMessage[2];
                }
                $stages[1]['code'] = 'deal_processing';
                $stages[2]['code'] = 'payment';
                $stages[3]['code'] = 'signing_and_registration';
                $stages[0]['substages'][0]['icon'] = Storage::url('public/images/icons/nav_bar/oplata_active.png');
                $stages[0]['substages'][1]['icon'] = Storage::url('public/images/icons/nav_bar/forma_sobstvennosti_active.png');

                $stages[0]['substages'][0]['status_message'] = $statusMessage[0];
                $stages[0]['substages'][1]['status_message'] = $statusMessage[1];

                $stages[0]['substages'][0]['icon_navbar'] = new IconNavbar(
                    number: 1,
                    activeIcon: Storage::url('public/images/icons/nav_bar/oplata_active.png'),
                    disableIcon: Storage::url('public/images/icons/nav_bar/oplata_disabled.png')
                );
                $stages[0]['substages'][1]['icon_navbar'] = new IconNavbar(
                    number: 2,
                    activeIcon: Storage::url('public/images/icons/nav_bar/forma_sobstvennosti_active.png'),
                    disableIcon: Storage::url('public/images/icons/nav_bar/forma_sobstvennosti_disabled.png')
                );
            }
        } elseif (($demand['stepName'] ?? '' == 'Сбор информации') && $demand['status']['code']) {
            $stages[0]['status'] = 'done';
            $stages[1]['status'] = 'wait';
            $stages[1]['message'] = 'Ожидайте подготовки договора';
            $stages[2]['status'] = 'closed';
            $stages[3]['status'] = 'closed';

            $stages[0]['code'] = 'transaction_terms';
            $stages[1]['code'] = 'deal_processing';
            $stages[2]['code'] = 'payment';
            $stages[3]['code'] = 'signing_and_registration';
        }

        if (($demand['stepName'] ?? '' == 'Сбор информации') && (($demand['status']['code'] ?? null) == 32)) {
            $stages[0]['status'] = 'done';
            $stages[1]['status'] = 'wait';
            $stages[1]['message'] = 'Ожидайте подготовки договора';
            $stages[2]['status'] = 'closed';
            $stages[3]['status'] = 'closed';
        }

        foreach ($stages as $stage) {
            $substages = [];

            foreach ($stage['substages']??[] as $substage) {
                $statusIcon = $substage['status_message']? !($substage['status_message'] == 'Необходимо указать') : true;

                $substages[] = new SaleSubstage(
                    number: $substage['number'] ?? null,
                    name: $substage['name'] ?? null,
                    status: StageStatus::active(),
                    icon: $substage['icon'] ?? null,
                    code: $substage['code'] ?? null,
                    statusMessage: $substage['status_message'] ?? null,
                    statusIcon: $statusIcon??null,
                    iconNavbar: $substage['icon_navbar']??null,
                );
            }

            if (isset($stage['status'])) {
                $icon = '';
                if ($stage['number'] == 1) {
                    if ($stage['status'] == 'active' || $stage['status'] == 'done' || $stage['status'] == 'wait') {
                        $icon = Storage::url('public/images/icons/stages/usloviya_sdelki_active.png');
                    } elseif ($stage['status'] == 'closed') {
                        $icon = Storage::url('public/images/icons/stages/usloviya_sdelki_disabled.png');
                    }
                } elseif ($stage['number'] == 2) {
                    if ($stage['status'] == 'active' || $stage['status'] == 'done' || $stage['status'] == 'wait') {
                        $icon = Storage::url('public/images/icons/stages/oformlenie_active.png');
                    } elseif ($stage['status'] == 'closed') {
                        $icon = Storage::url('public/images/icons/stages/oformlenie_disabled.png');
                    }
                } elseif ($stage['number'] == 3) {
                    if ($stage['status'] == 'closed') {
                        $icon = Storage::url('public/images/icons/stages/oplata_disabled.png');
                    }
                } elseif ($stage['number'] == 4) {
                    if ($stage['status'] == 'closed') {
                        $icon = Storage::url('public/images/icons/stages/podpisanie_disabled.png');
                    }
                } elseif ($stage['number'] == 5) {
                    $icon = Storage::url('public/images/icons/stages/dop_soglasheniya_disabled.png');
                } elseif ($stage['number'] == 6) {
                    $icon = Storage::url('public/images/icons/stages/doraschet_disabled.png');
                } elseif ($stage['number'] == 7) {
                    $icon = Storage::url('public/images/icons/stages/priemka_disabled.png');
                }
            }

            $saleStages[] = new SaleStage(
                number: $stage['number'],
                name: $stage['name'],
                status: $stage['status'] ?? '',
                substages: ($stage['status'] ?? '') != 'done' ? ($substages ?? []) : [],
                message: $stage['message'] ?? '',
                icon: $icon ?? $stage['icon'],
                code: $stage['code'] ?? '',
            );
        }

        return $saleStages;
    }

    public function makeContractStages(array $demand, array $characteristics = [], ?bool $isContractApprove = null): array
    {
        // phpcs:disable
        $saleStages = [];

        $stages[0]['number'] = 1;
        $stages[1]['number'] = 2;
        $stages[2]['number'] = 3;
        $stages[3]['number'] = 4;
        $stages[0]['name'] = 'Условия сделки';
        $stages[1]['name'] = 'Оформление сделки';
        $stages[2]['name'] = 'Оплата';
        $stages[3]['name'] = 'Подписание и регистрация';

        $personalOwnerships = collect($demand['jointOwners'] ?? null)?->where('ownerType.code', '=', OwnerType::personal()->value);
        $sharedOwnerships = collect($demand['jointOwners'] ?? null)?->where('ownerType.code', '=', OwnerType::shared()->value);
        $jointOwnerships = collect($demand['jointOwners'] ?? null)?->where('ownerType.code', '=', OwnerType::joint()->value);

        $filteredCharacteristics = collect($characteristics)->filter(function ($object) {
            if ($object->getArticleVariantTmCode() != 1) {
                return false;
            } {

            }
            return $object->getType()->value == '1048576';
        });

        $isEscrow = collect($characteristics)->filter(function ($object) {
            return $object->getIsEscrow() == true;
        });

        if (isset($demand['letterOfCreditBankId']) && $demand['letterOfCreditBankId'] != null) {
            $statusMessage[0] = $demand['paymentModeCode']['name'];
        } else {
            $statusMessage[0] = 'Необходимо указать';
        }

        if ($personalOwnerships->count() > 0) {
            $statusMessage[1] = 'Индивидуальная собственность';
        } elseif ($sharedOwnerships->count() > 0) {
            $statusMessage[1] = 'Совместная собственность';
        } elseif ($jointOwnerships->count() > 0) {
            $statusMessage[1] = 'Долевая собственность';
        } else {
            $statusMessage[1] = 'Необходимо указать';
        }

        if (isset($demand['baseFinishVariant'])) {
            $finishing = Finishing::where('finishing_id', '=', $demand['baseFinishVariant']['id'])->first();
            $statusMessage[2] = $finishing?->name;
        } else {
            $statusMessage[2] = 'Необходимо указать';
        }

        if (isset($demand['depositor_fiz_id'])) {
            $statusMessage[3] = 'Депонент указан';
        } else {
            $statusMessage[3] = 'Необходимо указать';
        }

        if ($demand['stepName']??'' == 'Сбор информации') {
            $stages[0]['status'] = 'done';
            $stages[1]['status'] = 'closed';
            $stages[2]['status'] = 'closed';
            $stages[3]['status'] = 'closed';

            if ($demand['articleOrders'][0]['serviceCode'] == 020020) {
                $stages[0]['code'] = 'transaction_terms';
                $stages[0]['substages'][0]['code'] = 'form_of_payment';
                $stages[0]['substages'][0]['number'] = '1';
                $stages[0]['substages'][0]['name'] = 'Форма оплаты';
                $stages[0]['substages'][0]['status_message'] = $statusMessage[0];
                $stages[1]['code'] = 'deal_processing';
                $stages[2]['code'] = 'payment';
                $stages[3]['code'] = 'signing_and_registration';

                $stages[0]['substages'][0]['icon'] = Storage::url('public/images/icons/nav_bar/oplata_active.png');
                $stages[0]['substages'][0]['icon_navbar'] = new IconNavbar(
                    number: 1,
                    activeIcon: Storage::url('public/images/icons/nav_bar/oplata_active.png'),
                    disableIcon: Storage::url('public/images/icons/nav_bar/oplata_disabled.png')
                );
            } elseif ($demand['articleOrders'][0]['serviceCode'] == 020030) {
                $stages[0]['code'] = 'transaction_terms';
                $stages[0]['substages'][0]['number'] = '1';
                $stages[0]['substages'][0]['code'] = 'form_of_payment';
                $stages[0]['substages'][0]['name'] = 'Форма оплаты';
                $stages[0]['substages'][1]['number'] = '2';
                $stages[0]['substages'][1]['code'] = 'type_of_ownership';
                $stages[0]['substages'][1]['name'] = 'Форма собственности';
                if (count($filteredCharacteristics) > 0) {
                    $stages[0]['substages'][2]['number'] = '3';
                    $stages[0]['substages'][2]['code'] = 'finishing';
                    $stages[0]['substages'][2]['name'] = 'Отделка';
                    $stages[0]['substages'][2]['icon'] = Storage::url('public/images/icons/nav_bar/otdelka_active.png');
                    $stages[0]['substages'][2]['status_message'] = $statusMessage[2];
                }
                $stages[1]['code'] = 'deal_processing';
                $stages[2]['code'] = 'payment';
                $stages[3]['code'] = 'signing_and_registration';
                $stages[0]['substages'][0]['icon'] = Storage::url('public/images/icons/nav_bar/oplata_active.png');
                $stages[0]['substages'][1]['icon'] = Storage::url('public/images/icons/nav_bar/forma_sobstvennosti_active.png');
                $stages[0]['substages'][0]['status_message'] = $statusMessage[0];
                $stages[0]['substages'][1]['status_message'] = $statusMessage[1];
                $stages[0]['substages'][0]['icon_navbar'] = new IconNavbar(
                    number: 1,
                    activeIcon: Storage::url('public/images/icons/nav_bar/oplata_active.png'),
                    disableIcon: Storage::url('public/images/icons/nav_bar/oplata_disabled.png')
                );
                $stages[0]['substages'][1]['icon_navbar'] = new IconNavbar(
                    number: 2,
                    activeIcon: Storage::url('public/images/icons/nav_bar/forma_sobstvennosti_active.png'),
                    disableIcon: Storage::url('public/images/icons/nav_bar/forma_sobstvennosti_disabled.png')
                );
                $stages[0]['substages'][2]['icon_navbar'] = new IconNavbar(
                    number: 3,
                    activeIcon: Storage::url('public/images/icons/nav_bar/otdelka_active.png'),
                    disableIcon: Storage::url('public/images/icons/nav_bar/otdelka_disabled.png')
                );
            } elseif ($demand['articleOrders'][0]['serviceCode'] == 020011) {
                $stages[0]['code'] = 'transaction_terms';
                $stages[0]['substages'][0]['number'] = '1';
                $stages[0]['substages'][0]['code'] = 'form_of_payment';
                $stages[0]['substages'][0]['name'] = 'Форма оплаты';
                $stages[0]['substages'][1]['number'] = '2';
                $stages[0]['substages'][1]['code'] = 'type_of_ownership';
                $stages[0]['substages'][1]['name'] = 'Форма собственности';
                if (count($filteredCharacteristics) > 0) {
                    $stages[0]['substages'][2]['number'] = '3';
                    $stages[0]['substages'][2]['code'] = 'finishing';
                    $stages[0]['substages'][2]['name'] = 'Отделка';
                    $stages[0]['substages'][2]['icon'] = Storage::url('public/images/icons/nav_bar/otdelka_active.png');
                    $stages[0]['substages'][2]['status_message'] = $statusMessage[2];
                    $stages[0]['substages'][2]['icon_navbar'] = new IconNavbar(
                        number: 3,
                        activeIcon: Storage::url('public/images/icons/nav_bar/otdelka_active.png'),
                        disableIcon: Storage::url('public/images/icons/nav_bar/otdelka_disabled.png')
                    );
                }
                if (count($isEscrow) > 0) {
                    $stages[0]['substages'][3]['number'] = '4';
                    $stages[0]['substages'][3]['code'] = 'deponent';
                    $stages[0]['substages'][3]['name'] = 'Общая информация';
                    $stages[0]['substages'][3]['icon'] = Storage::url('public/images/icons/nav_bar/rekvizity scheta_active.png');
                    $stages[0]['substages'][3]['status_message'] = $statusMessage[3];
                    $stages[0]['substages'][3]['icon_navbar'] = new IconNavbar(
                        number: 3,
                        activeIcon: Storage::url('public/images/icons/nav_bar/rekvizity_scheta_active.png'),
                        disableIcon: Storage::url('public/images/icons/nav_bar/rekvizity_scheta_disabled.png')
                    );
                }
                $stages[1]['code'] = 'deal_processing';
                $stages[2]['code'] = 'payment';
                $stages[3]['code'] = 'signing_and_registration';
                $stages[0]['substages'][0]['icon'] = Storage::url('public/images/icons/nav_bar/oplata_active.png');
                $stages[0]['substages'][1]['icon'] = Storage::url('public/images/icons/nav_bar/forma_sobstvennosti_active.png');
                $stages[0]['substages'][0]['status_message'] = $statusMessage[0];
                $stages[0]['substages'][1]['status_message'] = $statusMessage[1];

                $stages[0]['substages'][0]['icon_navbar'] = new IconNavbar(
                    number: 1,
                    activeIcon: Storage::url('public/images/icons/nav_bar/oplata_active.png'),
                    disableIcon: Storage::url('public/images/icons/nav_bar/oplata_disabled.png')
                );
                $stages[0]['substages'][1]['icon_navbar'] = new IconNavbar(
                    number: 2,
                    activeIcon: Storage::url('public/images/icons/nav_bar/forma_sobstvennosti_active.png'),
                    disableIcon: Storage::url('public/images/icons/nav_bar/forma_sobstvennosti_disabled.png')
                );
            } else {
                $stages[0]['code'] = 'transaction_terms';
                $stages[0]['substages'][0]['number'] = '1';
                $stages[0]['substages'][0]['code'] = 'form_of_payment';
                $stages[0]['substages'][0]['name'] = 'Форма оплаты';
                $stages[0]['substages'][1]['number'] = '2';
                $stages[0]['substages'][1]['code'] = 'type_of_ownership';
                $stages[0]['substages'][1]['name'] = 'Форма собственности';
                if (count($filteredCharacteristics) > 0) {
                    $stages[0]['substages'][2]['number'] = '3';
                    $stages[0]['substages'][2]['code'] = 'finishing';
                    $stages[0]['substages'][2]['name'] = 'Отделка';
                    $stages[0]['substages'][2]['icon'] = Storage::url('public/images/icons/nav_bar/otdelka_active.png');
                    $stages[0]['substages'][2]['icon_navbar'] = new IconNavbar(
                        number: 3,
                        activeIcon: Storage::url('public/images/icons/nav_bar/otdelka_active.png'),
                        disableIcon: Storage::url('public/images/icons/nav_bar/otdelka_disabled.png')
                    );
                    $stages[0]['substages'][2]['status_message'] = $statusMessage[2];
                }
                $stages[1]['code'] = 'deal_processing';
                $stages[2]['code'] = 'payment';
                $stages[3]['code'] = 'signing_and_registration';
                $stages[0]['substages'][0]['icon'] = Storage::url('public/images/icons/nav_bar/oplata_active.png');
                $stages[0]['substages'][1]['icon'] = Storage::url('public/images/icons/nav_bar/forma_sobstvennosti_active.png');

                $stages[0]['substages'][0]['status_message'] = $statusMessage[0];
                $stages[0]['substages'][1]['status_message'] = $statusMessage[1];

                $stages[0]['substages'][0]['icon_navbar'] = new IconNavbar(
                    number: 1,
                    activeIcon: Storage::url('public/images/icons/nav_bar/oplata_active.png'),
                    disableIcon: Storage::url('public/images/icons/nav_bar/oplata_disabled.png')
                );
                $stages[0]['substages'][1]['icon_navbar'] = new IconNavbar(
                    number: 2,
                    activeIcon: Storage::url('public/images/icons/nav_bar/forma_sobstvennosti_active.png'),
                    disableIcon: Storage::url('public/images/icons/nav_bar/forma_sobstvennosti_disabled.png')
                );
            }
        } elseif (($demand['stepName'] ?? '' == 'Сбор информации') && $demand['status']['code']) {
            $stages[0]['status'] = 'done';
            $stages[1]['status'] = 'wait';
            $stages[1]['message'] = 'Ожидайте подготовки договора';
            $stages[2]['status'] = 'closed';
            $stages[3]['status'] = 'closed';

            $stages[0]['code'] = 'transaction_terms';
            $stages[1]['code'] = 'deal_processing';
            $stages[2]['code'] = 'payment';
            $stages[3]['code'] = 'signing_and_registration';
        }

        if (($demand['stepName'] ?? '' == 'Сбор информации') && (($demand['status']['code'] ?? null) == 32)) {
            $stages[0]['status'] = 'done';
            $stages[1]['status'] = 'wait';
            $stages[1]['message'] = 'Ожидайте подготовки договора';
            $stages[2]['status'] = 'closed';
            $stages[3]['status'] = 'closed';
        }

        if (($demand['paymentPlan'][0]['sumPayment'] ?? null) > 0) {
            $stages[0]['status'] = 'done';
            $stages[1]['status'] = 'done';
            $stages[2]['status'] = 'done';
            $stages[3]['status'] = 'done';
        } elseif (($demand['stepName'] ?? '') == 'Регистрация') {
            $stages[0]['status'] = 'done';
            $stages[1]['status'] = 'done';
            $stages[2]['status'] = 'active';
            $stages[3]['status'] = 'active';
        } elseif ($isContractApprove == false && (($demand['stepName'] ?? '') == 'Подготовка пакета документов')) {
            $stages[0]['status'] = 'done';
            $stages[1]['status'] = 'active';
            $stages[1]['substages'][0]['status'] = 'active';
            $stages[1]['substages'][1]['status'] = 'closed';
            $stages[1]['substages'][2]['status'] = 'closed';
            $stages[2]['status'] = 'closed';
            $stages[3]['status'] = 'closed';

            $stages[0]['code'] = 'transaction_terms';
            $stages[1]['code'] = 'deal_processing';
            $stages[1]['substages'][0]['code'] = 'contract_agreement';
            $stages[1]['substages'][1]['code'] = 'sign_release';
            $stages[1]['substages'][2]['code'] = 'upload_documents';
            $stages[2]['code'] = 'payment';
            $stages[3]['code'] = 'signing_and_registration';

            $stages[1]['substages'][0]['number'] = 1;
            $stages[1]['substages'][1]['number'] = 2;
            $stages[1]['substages'][2]['number'] = 3;

            $stages[1]['substages'][0]['name'] = 'Ознакомление с договором';
            $stages[1]['substages'][1]['name'] = 'Выпуск ЭП';
            $stages[1]['substages'][2]['name'] = 'Загрузка документов';

            $stages[1]['substages'][0]['status_message'] = false;
            $stages[1]['substages'][1]['status_message'] = false;
            $stages[1]['substages'][2]['status_message'] = false;

            $stages[1]['substages'][0]['icon'] = Storage::url('public/images/icons/substages_oformlenie/dogovor_active.png');
            $stages[1]['substages'][1]['icon'] = Storage::url('public/images/icons/substages_oformlenie/vypusk_ep_active.png');
            $stages[1]['substages'][2]['icon'] = Storage::url('public/images/icons/substages_oformlenie/zagruzka_dokumentov_active.png');
        } elseif ($isContractApprove == true || ((($demand['stepName'] ?? '') != 'Подготовка пакета документов') && $isContractApprove == false)) {
            $stages[0]['status'] = 'done';
            $stages[1]['status'] = 'active';
            $stages[1]['substages'][0]['status'] = 'done';
            $stages[1]['substages'][1]['status'] = 'active';
            $stages[1]['substages'][2]['status'] = 'active';
            $stages[2]['status'] = 'active';
            $stages[3]['status'] = 'active';

            $stages[1]['substages'][0]['number'] = 1;
            $stages[1]['substages'][1]['number'] = 2;
            $stages[1]['substages'][2]['number'] = 3;

            $stages[1]['substages'][0]['name'] = 'Ознакомление с договором';
            $stages[1]['substages'][1]['name'] = 'Выпуск ЭП';
            $stages[1]['substages'][2]['name'] = 'Загрузка документов';

            $stages[1]['substages'][0]['code'] = 'contract_agreement';
            $stages[1]['substages'][1]['code'] = 'sign_release';
            $stages[1]['substages'][2]['code'] = 'upload_documents';

            $stages[1]['substages'][0]['status_message'] = false;
            $stages[1]['substages'][1]['status_message'] = false;
            $stages[1]['substages'][2]['status_message'] = false;

            $stages[1]['substages'][0]['icon'] = Storage::url('public/images/icons/substages_oformlenie/dogovor_active.png');
            $stages[1]['substages'][1]['icon'] = Storage::url('public/images/icons/substages_oformlenie/vypusk_ep_active.png');
            $stages[1]['substages'][2]['icon'] = Storage::url('public/images/icons/substages_oformlenie/zagruzka_dokumentov_active.png');
        }

        foreach ($stages as $stage) {
            $substages = [];

            foreach ($stage['substages']??[] as $substage) {
                if (isset($substage['status_message'])) {
                    $statusIcon = $substage['status_message']? !($substage['status_message'] == 'Необходимо указать') : true;
                }

                if ($stage['number'] == 2) {
                    $statusIcon = false;
                }

                $substages[] = new SaleSubstage(
                    number: $substage['number'] ?? null,
                    name: $substage['name'] ?? null,
                    status: isset($substage['status'])? StageStatus::from($substage['status']): null,
                    icon: $substage['icon'] ?? null,
                    code: $substage['code'] ?? null,
                    statusMessage: $substage['status_message'] ?? null,
                    statusIcon: $statusIcon??null,
                    iconNavbar: $substage['icon_navbar']??null,
                );
            }

            if (isset($stage['status'])) {
                $icon = '';
                if ($stage['number'] == 1) {
                    if ($stage['status'] == 'active' || $stage['status'] == 'done' || $stage['status'] == 'wait') {
                        $icon = Storage::url('public/images/icons/stages/usloviya_sdelki_active.png');
                    } elseif ($stage['status'] == 'closed') {
                        $icon = Storage::url('public/images/icons/stages/usloviya_sdelki_disabled.png');
                    }
                } elseif ($stage['number'] == 2) {
                    if ($stage['status'] == 'active' || $stage['status'] == 'done' || $stage['status'] == 'wait') {
                        $icon = Storage::url('public/images/icons/stages/oformlenie_active.png');
                    } elseif ($stage['status'] == 'closed') {
                        $icon = Storage::url('public/images/icons/stages/oformlenie_disabled.png');
                    }
                } elseif ($stage['number'] == 3) {
                    if ($stage['status'] == 'active' || $stage['status'] == 'done' || $stage['status'] == 'wait') {
                        $icon = Storage::url('public/images/icons/stages/oplata_active.png');
                    } elseif ($stage['status'] == 'closed') {
                        $icon = Storage::url('public/images/icons/stages/oplata_disabled.png');
                    }
                } elseif ($stage['number'] == 4) {
                    if ($stage['status'] == 'active' || $stage['status'] == 'done' || $stage['status'] == 'wait') {
                        $icon = Storage::url('public/images/icons/stages/podpisanie_active.png');
                    } elseif ($stage['status'] == 'closed') {
                        $icon = Storage::url('public/images/icons/stages/podpisanie_disabled.png');
                    }
                }
            }

            $saleStages[] = new SaleStage(
                number: $stage['number'],
                name: $stage['name'],
                status: $stage['status'] ?? '',
                substages: ($stage['status'] ?? '') != 'done' ? ($substages ?? []) : [],
                message: $stage['message'] ?? '',
                icon: $icon ?? '',
                code: $stage['code'] ?? '',
            );
        }

        return $saleStages;
    }


    public function MakeAdditionalContractStages(Contract $contracts): array
    {
        // phpcs:disable
        $saleStages = [];

        $stages[0]['number'] = 1;
        $stages[1]['number'] = 2;
        $stages[0]['name'] = 'Выпуск ЭП';
        $stages[1]['name'] = 'Подписание и регистрация';
        $stages[0]['code'] = 'sign_release';
        $stages[1]['code'] = 'signing_and_registration';
        $stages[0]['message'] = null;
        $stages[1]['message'] = null;

        if ($contracts->getStepName() == 'Подписание договора') {
            $stages[0]['status'] = 'active';
            $stages[1]['status'] = 'active';
        } elseif ($contracts->getStepName() == 'Регистрация') {
            $stages[0]['status'] = 'done';
            $stages[1]['status'] = 'active';
        } elseif ($contracts->getRegistrationDate() != null && $contracts->getRegistrationNumber() != null) {
            $stages[0]['status'] = 'done';
            $stages[1]['status'] = 'done';
        }

        if ($stages[0]['status'] == 'closed') {
            $stages[0]['icon'] = Storage::url('public/images/icons/stages/usloviya_sdelki_active.png');
        } else {
            $stages[0]['icon'] = Storage::url('public/images/icons/stages/usloviya_sdelki_active.png');
        }

        if ($stages[1]['status'] == 'closed') {
            $stages[1]['icon'] = Storage::url('public/images/icons/stages/usloviya_sdelki_active.png');
        } else {
            $stages[1]['icon'] = Storage::url('public/images/icons/stages/usloviya_sdelki_active.png');
        }

        $saleStages = [];

        foreach ($stages as $stage) {
            $saleStages[] = new SaleStage(
                number: $stage['number'],
                name: $stage['name'],
                status: $stage['status'] ?? '',
                substages: null,
                message: $stage['message'] ?? '',
                icon: $icon ?? '',
                code: $stage['code'] ?? '',
            );
        }

        return $saleStages;
    }
}
