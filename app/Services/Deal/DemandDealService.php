<?php

namespace App\Services\Deal;

use App\Http\Admin\Controllers\FinishingImages\FinishingImagesController;
use App\Models\Finishing;
use App\Models\FinnishigImages;
use App\Models\Sales\CharacteristicSale\CharacteristicSale;
use App\Models\Sales\CharacteristicSale\CharacteristicSaleType;
use App\Models\Sales\Demand\Demand;
use App\Models\Sales\DiscountType;
use App\Models\Sales\PaymentMode;
use App\Models\User\User;
use App\Services\Deal\Exceptions\ChangingTypeOfPaymenNotAllowedException;
use App\Services\Deal\Exceptions\FinishHasAlreadyException;
use App\Services\Deal\Exceptions\FinishingCannotBeAppliedException;
use App\Services\Deal\Exceptions\MissingPaymentModeCodeException;
use App\Services\Deal\Exceptions\NotAllowedFinishingForThisObjectException;
use App\Services\Deal\Exceptions\PeriodOfFinishingNotDefinedException;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\Sales\Demand\DemandRepository;
use App\Services\Sales\Property\PropertyRepository;
use Carbon\Carbon;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Exception;

/**
 * Class DemandDealService
 *
 * @package App\Services\Deal
 */
class DemandDealService
{
    public function __construct(
        private DemandDealRepository $demandDealRepository,
        private DemandRepository $demandRepository,
        private PropertyRepository $propertyRepository,
        private DynamicsCrmClient $dynamicsCrmClient,
    ) {
    }

    /**
     * @param Demand $demand
     * @param string $finishId
     * @throws FinishHasAlreadyException
     * @throws FinishingCannotBeAppliedException
     * @throws PeriodOfFinishingNotDefinedException
     * @throws \App\Services\DynamicsCrm\Exceptions\BadRequestException
     * @throws \App\Services\DynamicsCrm\Exceptions\NotFoundException
     * @throws NotAllowedFinishingForThisObjectException
     */
    public function addFinishVariant(Demand $demand, string $finishId)
    {
        if ($demand->getBaseFinishVariant() == null) {
            $property = $this->propertyRepository->getById($demand->getArticleId());

            // phpcs:disable
            if ($property->getArticleVariantTm1Code() != 1) {
                throw new NotAllowedFinishingForThisObjectException();
            } elseif ($property->getArticleVariantTm1Code() == 1) {
                if ($property->getFinishingSalesStop() == null && $property->getFinishingSalesStart() == null) {
                    throw new PeriodOfFinishingNotDefinedException();
                } elseif (!Carbon::today()->betweenIncluded($property->getFinishingSalesStart(), $property->getFinishingSalesStop())) {
                    throw new FinishingCannotBeAppliedException();
                } elseif (Carbon::today()->betweenIncluded($property->getFinishingSalesStart(), $property->getFinishingSalesStop())) {

                } elseif (Carbon::now() < $property->getFinishingSalesStart() || Carbon::now() > $property->getFinishingSalesStart()) {
                    throw new FinishingCannotBeAppliedException();
                }
            }
            // phpcs:enable
        } else {
            throw new FinishHasAlreadyException();
        }

        if ($demand->isFinishingAvailable()) {
            $this->demandDealRepository->addFinishVariant($demand->getId(), $finishId);
        } else {
            throw new FinishingCannotBeAppliedException();
        }
    }

    /**
     * @param Demand $demand
     * @param $paymentType
     * @param User $user
     * @return void
     * @throws ChangingTypeOfPaymenNotAllowedException
     * @throws MissingPaymentModeCodeException
     * @throws \App\Services\DynamicsCrm\Exceptions\BadRequestException
     * @throws \App\Services\DynamicsCrm\Exceptions\NotFoundException
     */
    public function setPaymentType(Demand $demand, $paymentType, User $user)
    {
        // phpcs:disable
        if (!$demand->getBindedCharacteristicSales()) {
            if (count($demand->getPaymentPlans()) > 1) {
                throw new ChangingTypeOfPaymenNotAllowedException();
            }
            if ($demand->getArticlePrice() != $demand->getSumOpportunityMinusDiscount()) {
                throw new ChangingTypeOfPaymenNotAllowedException();
            }
        }

        if ($demand->getBindedCharacteristicSales() != null) {
            if ($demand->getBindedCharacteristicSales()[0]['processingType']['code'] == CharacteristicSaleType::instalments() &&
                $demand->getArticlePrice() != $demand->getSumOpportunityMinusDiscount()) {
                throw new ChangingTypeOfPaymenNotAllowedException();
            }
            if ($demand->getBindedCharacteristicSales()[0]['processingType']['code'] == CharacteristicSaleType::discount() &&
                count($demand->getPaymentPlans()) > 1) {
                throw new ChangingTypeOfPaymenNotAllowedException();
            }
        }

        if (!$demand->getPaymentMode()) {
            throw new MissingPaymentModeCodeException();
        }

        if (PaymentMode::from($paymentType) === PaymentMode::full() && PaymentMode::from($demand->getPaymentMode()->value) === PaymentMode::mortgage()) {
            $banksId = [
                'hypothecBankId' => '00000000-0000-0000-0000-000000000000',
                'letterOfCreditBankId' => '00000000-0000-0000-0000-000000000000',
            ];

            $this->demandDealRepository->putDemand($demand->getId(), $banksId);

            $demand = $this->findDemand($demand->getId(), $user);

            foreach ($demand->getJointOwners() as $jointOwner) {
                if ($jointOwner->getRole()->value == 8) {
                    $this->dynamicsCrmClient->deleteJointOwner($demand->getId(), $jointOwner->getId());
                }
            }
        } elseif (PaymentMode::from($paymentType) === PaymentMode::full() && PaymentMode::from($demand->getPaymentMode()->value) === PaymentMode::instalment()) {
            $characteristicIds = [];
            foreach ($demand->getBindedCharacteristicSales() as $characteristicSale) {
                if (CharacteristicSaleType::from($characteristicSale['processingType']['code']) === CharacteristicSaleType::instalments()) {
                    $characteristicIds[] = ['id' => $characteristicSale['id']];
                }
            }

            $this->demandDealRepository->characteristicSalesClear($demand->getId(), $characteristicIds);

            $characteristicsWithDiscount = [];
            foreach ($demand->getCharacteristics() as $characteristic) {
                if ($characteristic->getType()->value == CharacteristicSaleType::discount()->value) {
                    $characteristicsWithDiscount[] = $characteristic;
                }
            }

            if (count($characteristicsWithDiscount) == 1) {
                $characteristicSaleId['id'] = $characteristicsWithDiscount[0]->getId();
            } elseif (count($characteristicsWithDiscount) > 1) {
                $maxDiscount = 0;
                foreach ($characteristicsWithDiscount as $characteristic) {
                    $discount = 0;
                    if ($characteristic->getType()->value == DiscountType::percentFromTotal()->value) {
                        $discount = $demand->getArticlePrice() * $characteristic->getDiscountPercent();
                    }
                    if ($characteristic->getDiscountType()->value == DiscountType::amountFromTotal()->value) {
                        $discount = $demand->getArticlePrice() - $characteristic->getDiscountPercent();
                    }
                    if ($discount > $maxDiscount) {
                        $maxDiscount = $discount;
                        $characteristicSaleId['id'] = $characteristic->getId();
                    }
                }
            }

            if (isset($characteristicSaleId)) {
                $this->demandDealRepository->characteristicSalesDemand($demand->getId(), $characteristicIds);
            }
        } elseif (PaymentMode::from($paymentType) === PaymentMode::mortgage() && PaymentMode::from($demand->getPaymentMode()->value) === PaymentMode::full()) {
            $characteristicsWithDiscount = [];
            foreach ($demand->getCharacteristics() as $characteristic) {
                if ($characteristic->getType()->value == CharacteristicSaleType::discount()->value) {
                    $characteristicsWithDiscount[] = $characteristic;
                }
            }

            $characteristicSaleId = [];
            if (count($characteristicsWithDiscount) == 1) {
                $characteristicSaleId['id'] = $characteristicsWithDiscount[0]->getId();
            } elseif (count($characteristicsWithDiscount) > 1) {
                $maxDiscount = 0;
                foreach ($characteristicsWithDiscount as $characteristic) {
                    $discount = 0;
                    if ($characteristic->getDiscountType() === DiscountType::percentFromTotal()) {
                        $discount = $demand->getArticlePrice() * $characteristic->getDiscountPercent();
                    }
                    if ($characteristic->getDiscountType() === DiscountType::amountFromTotal()) {
                        $discount = $demand->getArticlePrice() - $characteristic->getDiscountPercent();
                    }
                    if ($discount > $maxDiscount) {
                        $maxDiscount = $discount;
                        $characteristicSaleId['id'] = $characteristic->getId();
                    }
                }
            }

            if (count($characteristicSaleId) != 0) {
                $this->demandDealRepository->characteristicSalesDemand($demand->getId(), $characteristicSaleId);
            }

            $openDemand = $this->demandDealRepository->getDemandByOpenStatus($user);

            if (count($openDemand) != 0) {
                $filteredDemand = array_filter($openDemand, function ($fdemand) use ($demand) {
                    return $fdemand['demandMainId'] ?? '' === $demand->getId();
                });

                if ($filteredDemand) {
                    $filteredDemand = $filteredDemand[0];
                    if ($filteredDemand['hypothecBankId']) {
                        $banksId = [
                            'hypothecBankId' => $filteredDemand['hypothecBankId'],
                            'letterOfCreditBankId' => $filteredDemand['hypothecBankId'],
                        ];

                        $this->demandDealRepository->setDealBank($demand->getId(), 'HypothecBank', $demand->getHypothecBankId());
                        $this->demandDealRepository->putDemand($demand->getId(), $banksId);
                    } else {
                        $banksId = [
                            'hypothecBankId' => '0af0ffcb-060d-ea11-942d-005056bf3b92',
                        ];

                        $this->demandDealRepository->setDealBank($demand->getId(), 'HypothecBank', '0af0ffcb-060d-ea11-942d-005056bf3b92');
                        $this->demandDealRepository->putDemand($demand->getId(), $banksId);
                    }
                } else {
                    $banksId = [
                        'hypothecBankId' => '0af0ffcb-060d-ea11-942d-005056bf3b92',
                    ];

                    $this->demandDealRepository->setDealBank($demand->getId(), 'HypothecBank', '0af0ffcb-060d-ea11-942d-005056bf3b92');
                    $this->demandDealRepository->putDemand($demand->getId(), $banksId);
                }

                foreach ($demand->getJointOwners() as $jointOwner) {
                    if ($jointOwner->getRole()->value == 8) {
                        $this->demandDealRepository->createLeadCustomer($demand->getId(), $jointOwner->getId());
                    }
                }
            }
        } elseif (PaymentMode::from($paymentType) === PaymentMode::mortgage() && PaymentMode::from($demand->getPaymentMode()->value) === PaymentMode::instalment()) {
            $characteristicIds = [];
            foreach ($demand->getBindedCharacteristicSales() as $characteristicSale) {
                if (CharacteristicSaleType::from($characteristicSale['processingType']['code']) === CharacteristicSaleType::instalments()) {
                    $characteristicIds[] = ['id' => $characteristicSale['id']];
                }
            }
            $this->demandDealRepository->characteristicSalesClear($demand->getId(), $characteristicIds);

            $characteristicsWithDiscount = [];
            foreach ($demand->getCharacteristics() as $characteristic) {
                if ($characteristic->getType()->value == CharacteristicSaleType::discount()->value) {
                    $characteristicsWithDiscount[] = $characteristic;
                }
            }

            $characteristicSaleId = [];
            if (count($characteristicsWithDiscount) == 1) {
                $characteristicSaleId['id'] = $characteristicsWithDiscount[0]->getId();
            } elseif (count($characteristicsWithDiscount) > 1) {
                $maxDiscount = 0;
                foreach ($characteristicsWithDiscount as $characteristic) {
                    $discount = 0;
                    if ($characteristic->getDiscountType() === DiscountType::percentFromTotal()) {
                        $discount = $demand->getArticlePrice() * $characteristic->getDiscountPercent();
                    }
                    if ($characteristic->getDiscountType() === DiscountType::amountFromTotal()) {
                        $discount = $demand->getArticlePrice() - $characteristic->getDiscountPercent();
                    }
                    if ($discount > $maxDiscount) {
                        $maxDiscount = $discount;
                        $characteristicSaleId['id'] = $characteristic->getId();
                    }
                }
            }

            if (count($characteristicSaleId) != 0) {
                $this->demandDealRepository->characteristicSalesDemand($demand->getId(), $characteristicSaleId);
            }

            $openDemand = $this->demandDealRepository->getDemandByOpenStatus($user)['demandList'] ?? 0;

            if (count($openDemand) != 0) {
                $filteredDemand = array_filter($openDemand, function ($fdemand) use ($demand) {
                    return ($fdemand['demandMainId'] ?? '') === $demand->getId();
                });

                if ($filteredDemand) {
                    if ($filteredDemand['hypothecBankId']) {
                        $banksId = [
                            'hypothecBankId' => $filteredDemand['hypothecBankId'],
                            'letterOfCreditBankId' => $filteredDemand['hypothecBankId'],
                        ];

                        $this->demandDealRepository->setDealBank($demand->getId(), 'HypothecBank', $demand->getHypothecBankId());
                        $this->demandDealRepository->putDemand($demand->getId(), $banksId);
                    } else {
                        $banksId = [
                            'hypothecBankId' => '0af0ffcb-060d-ea11-942d-005056bf3b92',
                        ];

                        $this->demandDealRepository->setDealBank($demand->getId(), 'HypothecBank', '0af0ffcb-060d-ea11-942d-005056bf3b92');
                        $this->demandDealRepository->putDemand($demand->getId(), $banksId);
                    }
                } else {
                    $banksId = [
                        'hypothecBankId' => '0af0ffcb-060d-ea11-942d-005056bf3b92',
                    ];

                    $this->demandDealRepository->setDealBank($demand->getId(), 'HypothecBank', '0af0ffcb-060d-ea11-942d-005056bf3b92');
                    $this->demandDealRepository->putDemand($demand->getId(), $banksId);
                }
            }

            foreach ($demand->getJointOwners() as $jointOwner) {
                if ($jointOwner->getRole()->value == 8) {
                    $this->demandDealRepository->createLeadCustomer($demand->getId(), $jointOwner->getId());
                }
            }
        }
    }

    /**
     * @param Demand $demand
     * @param string $installmetId
     * @return void
     * @throws ChangingTypeOfPaymenNotAllowedException
     * @throws MissingPaymentModeCodeException
     * @throws \App\Services\DynamicsCrm\Exceptions\BadRequestException
     * @throws \App\Services\DynamicsCrm\Exceptions\NotFoundException
     */
    public function setInstallmet(Demand $demand, string $installmetId)
    {
        if (!$demand->getBindedCharacteristicSales()) {
            if (count($demand->getPaymentPlans()) > 1) {
                throw new ChangingTypeOfPaymenNotAllowedException();
            }
            if ($demand->getArticlePrice() != $demand->getSumOpportunityMinusDiscount()) {
                throw new ChangingTypeOfPaymenNotAllowedException();
            }
        }
        // phpcs:disable
        if ($demand->getBindedCharacteristicSales()) {
            if ($demand->getBindedCharacteristicSales()[0]['processingType']['code'] == CharacteristicSaleType::instalments() &&
                $demand->getArticlePrice() != $demand->getSumOpportunityMinusDiscount()) {
                throw new ChangingTypeOfPaymenNotAllowedException();
            }
            if ($demand->getBindedCharacteristicSales()[0]['processingType']['code'] == CharacteristicSaleType::discount() &&
                count($demand->getPaymentPlans()) > 1) {
                throw new ChangingTypeOfPaymenNotAllowedException();
            }
        }
        // phpcs:enable

        if (!$demand->getPaymentMode()) {
            throw new MissingPaymentModeCodeException();
        }

        if ($demand->getPaymentMode()->value == PaymentMode::full()) {
            $banksId = [
                'letterOfCreditBankId' => '00000000-0000-0000-0000-000000000000',
            ];

            $deal = $this->demandDealRepository->putDemand($demand->getId(), $banksId);

            $this->demandDealRepository->characteristicSalesDemand($demand->getId(), ['id' => $installmetId]);
        } elseif ($demand->getPaymentMode()->value == PaymentMode::mortgage()) {
            $banksId = [
                'hypothecBankId' => '00000000-0000-0000-0000-000000000000',
                'letterOfCreditBankId' => '00000000-0000-0000-0000-000000000000',
            ];

            $deal = $this->demandDealRepository->putDemand($demand->getId(), $banksId);

            $this->demandDealRepository->characteristicSalesDemand($demand->getId(), ['id' => $installmetId]);

            foreach ($demand->getJointOwners() as $jointOwner) {
                if ($jointOwner->getRole()->value == 8) {
                    $this->demandDealRepository->deleteLead($demand->getId(), $jointOwner->getId());
                }
            }
        } elseif ($demand->getPaymentMode()->value == PaymentMode::instalment()) {
            $this->demandDealRepository->characteristicSalesDemand($demand->getId(), ['id' => $installmetId]);
        }
    }

    /**
     * @param $demandId
     * @param $request
     * @return void
     * @throws \App\Services\DynamicsCrm\Exceptions\BadRequestException
     * @throws \App\Services\DynamicsCrm\Exceptions\NotFoundException
     */
    public function setDepositor($demandId, $request)
    {
        if ($request->type == 'fiz') {
            $depositor = [
                'DepositorfizId' => $request->depositor_id,
            ];
        } elseif ($request->type == 'ur') {
            $depositor = [
                'DepositorUrId' => $request->depositor_id,
            ];
        }

        $this->demandDealRepository->putDepositor($demandId, $depositor);
    }

    /**
     * @param $demand
     * @return array
     * @throws \App\Services\DynamicsCrm\Exceptions\BadRequestException
     * @throws \App\Services\DynamicsCrm\Exceptions\NotFoundException
     */
    public function getFinishing($demand): array
    {
        $finishings = [];

        /** @var Demand $demand */
        if ($demand->getArticleId()) {
            $characteristicSales = $this->demandDealRepository->getCharacteristicSales($demand->getArticleId());

            if ($demand->getBaseFinishVariant() != null) {
                $property = $this->propertyRepository->getById($demand->getArticleId());

                // phpcs:disable
                if ($property->getArticleVariantTm1Code() != 1) {
                    return [];
                } elseif ($property->getArticleVariantTm1Code() == 1) {
                    if ($property->getFinishingSalesStop() == null && $property->getFinishingSalesStart() == null) {
                        return [];
                    } elseif (!Carbon::today()->betweenIncluded($property->getFinishingSalesStart(), $property->getFinishingSalesStop())) {
                        return [];
                    } elseif (Carbon::now() < $property->getFinishingSalesStart() || Carbon::now() > $property->getFinishingSalesStop()) {
                        return [];
                    }
                }
                // phpcs:enable
            }

            foreach ($characteristicSales['characteristicSaleList'] as $characteristicSale) {
                if (isset($characteristicSale['processingType'])) {
                    if ($characteristicSale['processingType']['code'] == 1048576) {
                        $finishing = Finishing::where('finishing_id', '=', $characteristicSale['id'])->first();

                        if ($demand->getBaseFinishVariant()) {
                            if ($finishing) {
                                if ($demand->getBaseFinishVariant()->getId() == $characteristicSale['id']) {
                                    $finishings[] = [
                                        'id' => $characteristicSale['id'],
                                        'name' => $finishing?->name,
                                        'images' => $finishing?->images?->pluck('url'),
                                        'catalog' => $finishing?->catalog_url,
                                        'isSelected' => true
                                    ];
                                } else {
                                    $finishings[] = [
                                        'id' => $characteristicSale['id'],
                                        'name' => $characteristicSale['name'] ?? null,
                                        'images' => $finishing?->images?->pluck('url'),
                                        'catalog' => $finishing?->catalog_url,
                                        'isSelected' => false
                                    ];
                                }
                            } else {
                                if ($demand->getBaseFinishVariant()->getId() == $characteristicSale['id']) {
                                    $finishings[] = [
                                        'id' => $characteristicSale['id'] ?? null,
                                        'name' => $characteristicSale['name'] ?? null,
                                        'images' => null,
                                        'catalog' => null,
                                        'isSelected' => true
                                    ];
                                } else {
                                    $finishings[] = [
                                        'id' => $characteristicSale['id'] ?? null,
                                        'name' => $characteristicSale['name'] ?? null,
                                        'images' => null,
                                        'catalog' => null,
                                        'isSelected' => false
                                    ];
                                }
                            }
                        } else {
                            if (!$finishing?->exists) {
                                $finishings[] = [
                                    'id' => $characteristicSale['id'] ?? null,
                                    'name' => $characteristicSale['name'] ?? null,
                                    'images' => [],
                                    'catalog' => null,
                                    'isSelected' => false
                                ];
                            } else {
                                $finishings[] = [
                                    'id' => $characteristicSale['id'] ?? null,
                                    'name' => $finishing?->name,
                                    'images' => $finishing?->images?->pluck('url'),
                                    'catalog' => $finishing?->catalog_url,
                                    'isSelected' => false
                                ];
                            }
                        }
                    }
                }
            }
        }

        return $finishings;
    }

    protected function findDemand(string $id, User $user): Demand
    {
        try {
            return $this->demandRepository->getDemandById($id, $user);
        } catch (Exception) {
            throw new NotFoundHttpException('Demand not found.');
        }
    }
}
