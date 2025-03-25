<?php

namespace App\Services\Sales\Demand;

use App\Http\Api\External\V1\Controllers\Sales\BaseSalesController;
use App\Http\Api\External\V1\Requests\Sales\HypothecRequest;
use App\Http\Api\External\V1\Requests\Sales\SendContractDraftRequest;
use App\Http\Api\External\V1\Requests\Sales\TradeInRequest;
use App\Models\Banks;
use App\Models\Project\Project;
use App\Models\Role;
use App\Models\Sales\Bank\BankType;
use App\Models\Sales\CharacteristicSale\CharacteristicSaleType;
use App\Models\Sales\Customer\Customer;
use App\Models\Sales\Demand\Demand;
use App\Models\Sales\Demand\DemandBookingType;
use App\Models\Sales\Demand\DemandStatus;
use App\Models\Sales\Demand\DemandType;
use App\Models\Sales\DiscountType;
use App\Models\Sales\Hypothec\Hypothec;
use App\Models\Sales\MortgageApproval\MortgageApproval;
use App\Models\Sales\PayBookingTime;
use App\Models\Sales\PaymentMode;
use App\Models\Sales\SubType;
use App\Models\User\User;
use App\Services\Deal\DemandDealRepository;
use App\Services\Deal\Exceptions\ChangingTypeOfPaymenNotAllowedException;
use App\Services\Deal\Exceptions\MissingPaymentModeCodeException;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\Mortgage\MortgageClient;
use App\Services\Sales\Customer\CustomerRepository;
use App\Services\Sales\Deal\DealService;
use App\Services\Sales\Deal\Dto\CreateDealDto;
use App\Services\Sales\Demand\Dto\BorrowerDto;
use App\Services\Sales\Demand\Dto\CreateBookingContractDto;
use App\Services\Sales\Demand\Dto\CreateBookingDto;
use App\Services\Sales\Demand\Dto\CreateJointOwnerDto;
use App\Services\Sales\Demand\Dto\CreateJointOwnerMeetingDto;
use App\Services\Sales\Demand\Dto\CreateMortgageDemandDto;
use App\Services\Sales\Demand\Dto\SetTermsDto;
use App\Services\Sales\Demand\Exceptions\TooManyBookingAttemptsException;
use App\Services\Sales\MortgageApprovalRepository;
use App\Services\Sales\Property\Dto\PropertyBookingDto;
use App\Services\Sales\Property\PropertyRepository;
use Carbon\Carbon;
use Exception;
use http\Exception\InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class DemandService
 *
 * @package App\Services\Sales\Demand
 */
class DemandService extends BaseSalesController
{
    public function __construct(
        private int $bookingLimit,
        private DynamicsCrmClient $dynamicsCrmClient,
        private DealService $dealService,
        private CustomerRepository $customerRepository,
        private DemandDealRepository $demandDealRepository,
        private MortgageApprovalRepository $mortgageApprovalRepository,
        private MortgageClient $mortgageClient,
        private PropertyRepository $propertyRepository,
        private DemandRepository $demandRepository,
    ) {
        parent::__construct($this->demandRepository);
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws TooManyBookingAttemptsException
     */
    public function createDemand(PropertyBookingDto $propertyBookingDto, User $user): string
    {
        $demandsCount = $user->deals()
            ->byDemandStatus(DemandStatus::reservation())
            ->byDemandBookingType(DemandBookingType::paid())
            ->count();
        if ($demandsCount >= $this->bookingLimit) {
            throw new TooManyBookingAttemptsException();
        }

        $createBookingDto = new CreateBookingDto(
            type: DemandType::cash(),
            bookingType: DemandBookingType::free(),
            propertyBookingDto: $propertyBookingDto,
            user: $user,
        );
        $demand = $this->dynamicsCrmClient->createBooking($createBookingDto);

        $demand = $this->dynamicsCrmClient->getDemandById($demand['id'], $user);
        $characteristicSales = collect($demand['characteristicSales'] ?? [])
            ->where('processingType.code', '=', 8);
        $characteristicFinishing = collect($demand['characteristicSales'] ?? [])
            ->where('processingType.code', '=', 1048576);

        $countOfChararcteristcs = $characteristicSales->count();
        if ($countOfChararcteristcs == 1) {
            $this->dynamicsCrmClient->setDemandCharacteristic($demand['id'], $characteristicSales->first()['id']);
        } elseif ($countOfChararcteristcs > 1) {
            $discount = 0;
            $maxDiscount = 0;
            $characteristicSaleId = null;

            foreach ($characteristicSales as $characteristic) {
                if ($characteristic->discountTypeCode->code == DiscountType::percentFromTotal()->value) {
                    $discount = $demand['articlePrice'] * $characteristic['discountPercent'];
                }
                if ($characteristic->discountTypeCode->code == DiscountType::amountFromTotal()->value) {
                    $discount = $demand['articlePrice'] - $characteristic['discountSum'];
                }

                if ($discount > $maxDiscount) {
                    $maxDiscount = $discount;
                    $characteristicSaleId = $characteristic['id'];
                }
            }

            if ($characteristicSaleId != null) {
                $this->dynamicsCrmClient->setDemandCharacteristic($demand['id'], $characteristicSaleId);
            }
        }

        if ($characteristicFinishing->count() == 1) {
            $this->dynamicsCrmClient->addFinishVariant($demand['id'], $characteristicFinishing->id);
        }

        //TODO перенести в pay-booking
//        $createBookingContractDto = new CreateBookingContractDto(
//            demandId: $demand['id'],
//            bookingType: DemandBookingType::paid(),
//            propertyBookingDto: $propertyBookingDto,
//            user: $user,
//        );
//        $this->dynamicsCrmClient->createBookingContract($createBookingContractDto);

//        $createDealDto = new CreateDealDto(
//            user: $user,
//            demandId: $demand['id'],
//            propertyBookingDto: $propertyBookingDto,
//            demandStatus: DemandStatus::from($demand['status']['code']),
//            demandBookingType: DemandBookingType::paid(),
//            initialBeginDate: (new Carbon($demand['beginDate']))->addRealHours(3),
//            initialEndDate: (new Carbon($demand['endDate']))->addRealHours(3),
//        );
//        $this->dealService->createDeal($createDealDto);

        return $demand['id'];
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function createMortgageDemand(Demand $demand, User $user): string
    {
        $customer = $this->customerRepository->getById($user->crm_id);

        $createMortgageDemandDto = new CreateMortgageDemandDto(
            demand: $demand,
            type: DemandType::mortgage(),
            property: $demand->getProperty(),
            customer: $customer,
        );
        $mortgageDemand = $this->dynamicsCrmClient->createMortgageDemand($createMortgageDemandDto);

        $this->dealService->setMortgageDemandId($demand->getDeal(), $mortgageDemand['id']);

        return $mortgageDemand['id'];
    }

    public function analizeDemand(Demand $demand)
    {
        $demandSubTypeCode = $demand->getBookingType()->value;

        if ($demandSubTypeCode == SubType::free()->value) {
            $property = $this->dynamicsCrmClient->getPropertyById($demand->getArticleId());
            $isPremium = Project::whereJsonContains('crm_ids', (int)$property['address']['gk']['id'])
                ->first()?->is_premium;

            if ($isPremium) {
                $demandSubType = SubType::premium();
            } else {
                $demandSubType = SubType::paid();
            }

            $bookProp = Project::whereJsonContains('booking_property->gk_id', (int)$property['address']['gk']['id'])
                ->first();

            if ($bookProp) {
                $paidBookingPaymentTime = $bookProp['paid_booking_payment_time'];
            }

            $this->dynamicsCrmClient->setDemandSubType($demand->getId(), $demandSubType);

            return $paidBookingPaymentTime ?? null;
        }

        return null;
    }

    public function paidOnline(Demand $demand)
    {
        $this->dynamicsCrmClient->setDemandSubType($demand->getId(), SubType::paidOnline());
    }

    public function changeDemandSubType(string $demandId, array $data)
    {
        $this->dynamicsCrmClient->changeDemandSubType($demandId, $data);
    }

    public function tradeInRequest(TradeInRequest $request)
    {
        $this->dynamicsCrmClient->sendTradeInRequest($request);
    }

    public function setContractDraft(string $demandId, string $userCrmId)
    {
        $this->dynamicsCrmClient->setContractDraft($demandId, $userCrmId);
    }

    public function getJointOwnerDocuments(string $id)
    {
        return $this->dynamicsCrmClient->getJointOwnerDocuments($id);
    }

    /**
     * @param Demand $demand
     * @param string $id
     * @return array
     */
    public function getDeponent($object, string $id)
    {
        $depositor_document = [];
        $depositor_info = null;
        $ownerId = null;

        if ($object->getDepositorFizId() == null || $object->getDepositorUrId()) {
            $depositor_document['document_info'] = null;
        } else {
            $checkIn = null;

            foreach ($object->getJointOwners() as $jointOwner) {
                if ($jointOwner->getContactId() == $object->getDepositorFizId()) {
                    $checkIn = $jointOwner;
                    $depositor_info = $jointOwner;
                }
            }

            if ($checkIn) {
                $documents = $this->getJointOwnerDocuments($checkIn->getContactId());
                $ownerId = $checkIn->getContactId();

                if (!empty($documents['documentList'])) {
                    foreach ($documents['documentList'] as $document) {
                        if ($document['documentType']['code'] == "40015") {
                            $doc = $document;
                        }
                    }
                }
            }

            if (!isset($doc)) {
                $depositor_document['document_info'] = null;
            } else {
                $depositor_document['document_info'] = $doc;
            }
        }

        return [
            'depositorInfo' => $depositor_info,
            'depositorDocument' => $depositor_document,
            'ownerId' => $ownerId,
        ];
    }

    public function getBorrowers(Demand $demand)
    {
        $borrowers = [];

        /** @var Customer $jointOwner */
        foreach ($demand['jointOwners'] as $jointOwner) {
            if ($jointOwner->getRole()->value == 8) {
                $borrowers[] = new BorrowerDto(
                    id: $jointOwner->getId(),
                    jointOwnerId: $jointOwner->getJointOwnerId(),
                    // phpcs:disable
                    fullName: $jointOwner->getLastName() . '' . $jointOwner->getFirstName() . '' . $jointOwner->getMiddleName()
                    // phpcs:enable
                );
            };
        }

        return $borrowers;
    }

    public function setJointOwnerByDefault(Demand $demand, $customerId): void
    {

        foreach ($demand->getJointOwners() as $jointOwner) {
            if ($jointOwner->getId() != $customerId && $jointOwner->getRole()->equals(Role::client())) {
                $this->dynamicsCrmClient->deleteJointOwner($jointOwner->getId(), $demand->getId());
            } elseif ($jointOwner->getId() == $customerId) {
                $this->dynamicsCrmClient->setJointOwnerByDefault($demand->getId(), $jointOwner->getId());
            }
        }
    }

    public function setPaymentByDefault(Demand $demand, User $user): void
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


        if (PaymentMode::from($demand->getPaymentMode()->value) === PaymentMode::mortgage()) {
            $banksId = [
                'hypothecBankId' => '00000000-0000-0000-0000-000000000000',
                'letterOfCreditBankId' => '00000000-0000-0000-0000-000000000000',
            ];

            $this->demandDealRepository->putDemand($demand->getId(), $banksId);

            $demand = $this->findDemand($demand->getId());

            foreach ($demand->getJointOwners() as $jointOwner) {
                if ($jointOwner->getRole()->value == 8) {
                    $this->demandDealRepository->deleteLead($demand->getId(), $jointOwner->getId());
                }
            }

//            $maxDiscount = 0;
//            foreach ($demand->getCharacteristics() as $characteristic) {
//                $discount = 0;
//                if ($characteristic->getDiscountType() != null) {
//                    if (DiscountType::from($characteristic->getDiscountType()) === DiscountType::percentFromTotal()) {
//                        $discount = $demand->getArticlePrice() * $characteristic->getDiscountPercent();
//                    }
//                    if (DiscountType::from($characteristic->getDiscountType()) === DiscountType::amountFromTotal()) {
//                        $discount = $demand->getArticlePrice() - $characteristic->getDiscountPercent();
//                    }
//                }
//                if ($discount > $maxDiscount) {
//                    $maxDiscount = $discount;
//                    $characteristicSaleId['id'] = $characteristic->getId();
//                }
//            }
//            if (count($characteristicSaleId) != 0) {
//                $this->demandDealRepository->characteristicSalesDemand($demand->getId(), $characteristicSaleId);
//            }
        } elseif (PaymentMode::from($demand->getPaymentMode()->value) === PaymentMode::instalment()) {
//            $characteristicIds = [];
//            foreach ($demand->getBindedCharacteristicSales() as $characteristicSale) {
//                if (CharacteristicSaleType::from($characteristicSale['processingType']['code']) === CharacteristicSaleType::instalments()) {
//                    $characteristicIds[] = ['id' => $characteristicSale['id']];
//                }
//            }

            $banksId = [
                'letterOfCreditBankId' => '00000000-0000-0000-0000-000000000000',
            ];

            $this->demandDealRepository->putDemand($demand->getId(), $banksId);
//
//            $characteristicIds = [];
//            foreach ($demand->getBindedCharacteristicSales() as $characteristicSale) {
//                if (CharacteristicSaleType::from($characteristicSale['processingType']['code']) === CharacteristicSaleType::instalments()) {
//                    $characteristicIds[] = ['id' => $characteristicSale['id']];
//                }
//            }
//
//            $this->demandDealRepository->characteristicSalesClear($demand->getId(), $characteristicIds);
        } elseif (PaymentMode::from($demand->getPaymentMode()->value) === PaymentMode::full()) {
            $banksId = [
                'letterOfCreditBankId' => '00000000-0000-0000-0000-000000000000',
            ];

            $this->demandDealRepository->putDemand($demand->getId(), $banksId);
        }
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function setTerms(Demand $demand, SetTermsDto $dto): void
    {
        if ($demand->getIsLetterOfCredit()) {
            $this->setLetterOfCreditBank(
                $demand->getId(),
                $dto->letterOfCreditBankId,
                BankType::letterOfCredit()
            );
        }

        if ($demand->getProperty()->getIsEscrow()) {
            $this->dealService->setIsEscrowBankClient($demand->getDeal(), $dto->isEscrowBankClient);
        }

        if ($dto->paymentMode->equals(PaymentMode::instalment()) && $dto->instalmentId) {
            foreach ($demand->getProperty()->getInstalments() as $instalment) {
                if ($instalment->getId() == $dto->instalmentId) {
                    $this->dynamicsCrmClient->setDemandCharacteristic($demand->getId(), $dto->instalmentId);
                }
            }

            $this->dynamicsCrmClient->setDemandPaymentMode($demand->getId(), $dto->paymentMode);
        } elseif ($dto->paymentMode->equals(PaymentMode::mortgage())) {
            if ($dto->mortgageTerms->isDigital) {
                //TODO: we don't have this in CRM API
            } else {
                $this->dynamicsCrmClient->setDemandBank(
                    $demand->getId(),
                    $dto->mortgageTerms->bankId,
                    BankType::mortgage(),
                );
                $this->dynamicsCrmClient->setDemandPaymentMode($demand->getId(), $dto->paymentMode);
            }
        } elseif ($dto->paymentMode->equals(PaymentMode::full())) {
            $this->dynamicsCrmClient->setDemandPaymentMode($demand->getId(), $dto->paymentMode);
        }
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function setLetterOfCreditBank(string $demandId, string $bankId, BankType $type): void
    {
        $this->dynamicsCrmClient->setDemandBank($demandId, $bankId, $type);
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function setFinishing(string $demandId, string $finishingId): void
    {
        $this->dynamicsCrmClient->setDemandCharacteristic($demandId, $finishingId);
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function setBookingPaid(string $demandId): void
    {
        $this->dynamicsCrmClient->setDemandBookingPaid($demandId);
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function setFreeBooking(Demand $demand): void
    {
        $this->dynamicsCrmClient->setDemandFreeBooking($demand);
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getMortgageUrl(string $demandId): ?string
    {
        $data = $this->dynamicsCrmClient->getDemandMortgageUrl($demandId);

        return $data['surveyUrl'] ?? null;
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getMortgageApprovals(string $demandId): ?string
    {
        $data = $this->dynamicsCrmClient->getDemandMortgageUrl($demandId);

        return $data['surveyUrl'] ?? null;
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function setJointOwners(CreateJointOwnerDto $dto): void
    {
        foreach ($dto->jointOwners as $jointOwner) {
            $this->dynamicsCrmClient->setDemandJointOwners($dto->demandId, $jointOwner);
        }
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function setJointOwnerMeetings(CreateJointOwnerMeetingDto $dto): void
    {
        foreach ($dto->jointOwnerMeetings as $jointOwnerMeetingDto) {
            $this->dynamicsCrmClient->setDemandJointOwnerMeetings($jointOwnerMeetingDto);
        }
    }

    public function getHypothec(string $demandId, User $user)
    {
       $surveyUrl = $this->dynamicsCrmClient->getDemandHypothec($demandId)['surveyUrl'];
       $tokenManager = $this->mortgageClient->getAuthToken()?->loanOfficer_SignIn;

       if ($surveyUrl == null) {
            sleep(30);
            $surveyUrl = $this->dynamicsCrmClient->getDemandHypothec($demandId)['surveyUrl'];
       }

       $openDemands = $this->dynamicsCrmClient->getDemandsByStatus($user, DemandStatus::open());
       $openChildDemand = collect($openDemands['demandList'])->where('demandMainId', '=', $demandId)
           ->where('dhIsIntegration', '=', true)->sortByDesc('modifiedOn')->first();

       $dhUuid = $openChildDemand['dhUuId'];
       $phone = $openChildDemand['phone'];
       try {
            $tokenClient = $this->mortgageClient->getClientToken($phone, $dhUuid, $tokenManager)->getClientTokenWithoutPhoneVerification;
       } catch (\Throwable $throwable) {
            $surveyUrl = null;
       }

       if ($surveyUrl == null) {
            $body = [
               'survey_url' => '',
               'message' => 'Цифровая анкета не создана'
            ];
       } else {
            $body = [
               'survey_url' => "https://make.dvizh.io/b2c-auth?uuid=$dhUuid&ctoken=$tokenClient",
               'message' => '',
            ];
       }

       return $body;
    }

    public function storeHypothec(HypothecRequest $request, string $demandId, User $user)
    {
        $demand = $this->findDemand($demandId);
        $body = [
            'lastName' => $demand->getLastName(),
            'firstName' => $demand->getFirstName(),
            'middleName' => $demand->getMiddleName(),
            'birthDate' => $demand->getBirthDate()->format('d.m.Y'),
            'phone' => $demand->getPhone(),
            'email' => $demand->getEmail(),
            'demandMainId' => $demandId,
            'demandType' => [
                'code' => 16,
                'name' => 'Ипотека'
            ],
            'dhIsIntegration' => $request->get('dh_is_integration'),
            'subject' => 'Заявка на ипотеку из МП',
        ];

        if ($request->get('dh_is_integration') == false) {
            $body['dateApproval'] = $request->get('date_approval');
            $body['bankName'] = $request->get('bank_name');
            $body['representativeHypothecBank'] = null;

            if ($body['dateApproval'] == null || $body['bankName'] == null) {
                throw new InvalidArgumentException('Некорректные параметры запроса', 400);
            }
         } else {
            $body['representativeHypothecBank'] = $request->get('date_approval') . ' ' . $request->get('bank_name') . ' ' . $request->get('manager') . ' ' . $request->get('phone_manager') . ' ' . $request->get('email_manager');
        }

        $hypothec = $this->dynamicsCrmClient->storeHypothec($body);
        $demands = $this->dynamicsCrmClient->getDemandsByStatus($user, DemandStatus::open());
        $hypothecs = [];

        foreach ($demands['demandList'] as $demandCrm) {
            if (($demandCrm['demandMainId'] ?? '') == $demandId) {
                $hypothecs[] = $demandCrm;
            }
        }

        if (count($hypothecs) > 1) {
            $manager = collect($hypothecs)->sortByDesc('modifiedOn')->first();
        } else {
            $manager = null;
        }

        return [
            'demand_id' => $demandId,
            'id' => $hypothec['id'] ?? null,
            'manager_hyphotec' => [
                'first_name' => $hypothec['firstName'] ?? null,
                'last_name' => $hypothec['lastName'] ?? null,
                'middle_name' => $hypothec['middleName'] ?? null,
                'phone' => $hypothec['phone'] ?? null,
                'email' => $hypothec['email'] ?? null,
            ]
        ];
    }

    public function hypothecInfo(User $user, string $id) {
        // Родительская заявка п.2
        $demand = $this->findDemand($id);

        // Дочерняя заявка п.3
        $openDemands = $this->dynamicsCrmClient->getDemandsByStatus($user, DemandStatus::open());
        $typeHypothecDemand = null;
        $openChildDemands = collect($openDemands['demandList'])->where('demandMainId', '=', $id);

        if ($openChildDemands->count() > 1) {
            $openChildDemands = $openChildDemands->toArray();
            usort($openChildDemands, function ($claim1, $claim2) {
                return (Carbon::parse($claim1["modifiedOn"]) > Carbon::parse($claim2["modifiedOn"])) ? -1 : 1;
            });

            $openChildDemand = $openChildDemands[0];
        } elseif ($openChildDemands->count() == 1) {
            $openChildDemand = $openChildDemands->first();
        }

        // п.4
        $detailChildDemand = null;
        if (isset($openChildDemand)) {
            $detailChildDemand = $this->findDemand($openChildDemand['id']);

            if ($detailChildDemand->getDhSerialNumber() == null) {
                $typeHypothecDemand = 'manual';
            } else {
                $typeHypothecDemand = 'digital';
            }
        }

        // п.5
        $isManualApprove = null;

        if (strtoupper($demand->getHypothecBankId()) == '0AF0FFCB-060D-EA11-942D-005056BF3B92') {
            if ($detailChildDemand == null) {
                $typeHypothecDemand = null;
            } elseif ($detailChildDemand != null && $detailChildDemand->getDhSerialNumber() == null) {
                $typeHypothecDemand = 'manual';
                $isManualApprove = false;
            } elseif ($detailChildDemand != null && $detailChildDemand->getDhSerialNumber() != null) {
                $typeHypothecDemand = 'digital';
                $confirmBankHypothec = null;
                $isDigitalApprove = false;
            }
        } elseif (strtoupper($demand->getHypothecBankId()) != '0AF0FFCB-060D-EA11-942D-005056BF3B92') {
            if ($detailChildDemand == null) {
                $typeHypothecDemand = 'manual';
                $isManualApprove = true;
                $bankNameApproveManual = Banks::where('bank_id', '=', $demand->getHypothecBankId())->first()?->name;
            } elseif ($detailChildDemand != null && $detailChildDemand->getDhSerialNumber() == null) {
                $typeHypothecDemand = 'manual';
                $isManualApprove = true;
                $bankNameApproveManual = Banks::where('bank_id', '=', $demand->getHypothecBankId())->first()?->name;
            } elseif ($detailChildDemand != null && $detailChildDemand->getDhSerialNumber() != null) {
                $approvalList = null;
            }
        }

        // п.6
        $isDigitalApprove = null;
        if ($detailChildDemand != null) {
            $approvalList = $this->mortgageApprovalRepository->getAllByDemand($detailChildDemand);

            if ($approvalList != null) {
                $statuses = [
                    'Возвращена на доработку',
                    'Одобрена банком',
                    'Отказ банка',
                    'Отказ клиента',
                    'Отправлена в банк',
                ];

                $approvalListByStatuses = collect($approvalList)->filter(function ($mortgageApproval) use ($statuses) {
                    /** @var MortgageApproval $mortgageApproval */
                    return in_array($mortgageApproval->getDhStatusCode(), $statuses);
                })->toArray();
            }

            $approvalHypothecList = collect($approvalList)->where('hypothecBankId', '=', $demand->getHypothecBankId())->first()?->toArray();

            if ($typeHypothecDemand != null) {
                $typeHypothecDemand = 'digital';
            } else {
                $approvalHypothecList = null;
                $typeHypothecDemand = 'manual';
                $isManualApprove = true;
                $bankNameApproveManual = Banks::where('bank_id', '=', $demand->getHypothecBankId())->first()?->name;
            }
        }


        // п.6
//        $isDigitalApprove = null;
//        $approvalList = null;
//        if ($typeHypothecDemand == 'manual') {
//            $approvalList = null;
//            $confirmBankHypothec = null;
//        } elseif ($typeHypothecDemand == 'digital') {
//            if ($demand->getHypothecBankId() == null || $demand->getHypothecBankId() != '0AF0FFCB-060D-EA11-942D-005056BF3B92') {
//                $confirmBankHypothec = null;
//                $isDigitalApprove = false;
//
//                $approvalList = $this->mortgageApprovalRepository->getAllByDemand($demand);
//            }
//        } elseif ($typeHypothecDemand == 'digital') {
//            if ($demand->getHypothecBankId() != null || $demand->getHypothecBankId() != '0AF0FFCB-060D-EA11-942D-005056BF3B92') {
//                $approvalList = null;
//                $isDigitalApprove = true;
//                $confirmBank = null;
//
//                $confirmBanksHypothec = $this->mortgageApprovalRepository->getAllByDemand($demand);
//
//                foreach ($confirmBanksHypothec as $confirmBankHypothec) {
//                    if ($confirmBankHypothec->getId() == $demand->getHypothecBankId()) {
//                        $confirmBank = $confirmBankHypothec;
//                        break;
//                    }
//                }
//            }
//        }

        if ($detailChildDemand?->getIsDigitalTransaction()) {
            $isBorrowersAvailible = true;
        } else {
            $isBorrowersAvailible = false;
        }

        $borrowers = collect($demand->getJointOwners())->filter(function($jointOwner) {
            return $jointOwner->getRole()?->value == 8;
        });

        $mortgageOnlineInfo = null;
        if ($demand->getIsMortgageOnlineAvailable() && $typeHypothecDemand == null) {
            $mortgageOnlineInfo = 'Вы можете подать заявку на получение одобрения кредита в банках-партнерах.\nЕсли у вас уже есть предварительное одобрение кредита, выберите соответствующий пункт меню и укажите информацию';
        } elseif (!$demand->getIsMortgageOnlineAvailable() && $typeHypothecDemand == null) {
            $mortgageOnlineInfo = 'Для подачи заявки на одобрение кредита, оплатите бронь\n Стоимость бронирования вычитается из общей стоимости объекта при оформлении сделки. Если вы не получите одобрение кредита, стоимость бронирования будет возвращена.\n\nЕсли у вас уже есть предварительное одобрение кредита, выберите соответствующий пункт меню и укажите информацию';
        } elseif ($typeHypothecDemand == 'manual' && $isManualApprove == false) {
            $mortgageOnlineInfo = 'Заявка по одобренной ипотеке находится на рассмотрении. В ближайшее время с вами свяжется менеджер';
        } elseif ($typeHypothecDemand == 'digital' && $isDigitalApprove == false) {
            $mortgageOnlineInfo = 'Для получения одобрения кредита вам нужно заполнить универсальную анкету при помощи сервиса цифровой ипотеки ДВИЖ.\n\nПосле того, как анкета будет полностью заполнена и подписана, мы направим ее в банки-партнеры, а вы сможете отслеживать статусы получения одобрения и выбрать банк для получения кредита';
        }

        $infoBorrower = null;
        if ((($typeHypothecDemand == 'manual' && $isManualApprove == true) || ($typeHypothecDemand == 'digital' && $isDigitalApprove == true)) && $isBorrowersAvailible == true) {
            $infoBorrower = 'Указание созаемщиков является необязательным этапом. Если вы планируете добавить созаемщиков, не следует указывать таковых как участников сделки';
        }

        if ($detailChildDemand?->getArticleId() != null) {
            $article = $this->propertyRepository->getById($detailChildDemand->getArticleId());

            if ($article->getEscrowBankId() == null) {
                $escrowBankName = null;
            } else {
                $escrowBankName = Banks::where('bank_id', '=', $article->getEscrowBankId())?->name;
            }
        }

        return new Hypothec(
            isMortgageOnlineAvailable: $demand->getIsMortgageOnlineAvailable(),
            typeHypothecDemand: $typeHypothecDemand ?? null,
            isManualApprove: $isManualApprove ?? null,
            isDigitalApprove: $isDigitalApprove ?? false,
            approvalList: $approvalListByStatuses ?? null,
            confirmBankHypothec: $approvalHypothecList ?? null,
            isBorrowersAvailible: $isBorrowersAvailible,
            borrowers: $borrowers->count() != 0 ? $borrowers : [],
            mortageOnlineInfo: $mortgageOnlineInfo,
            infoBorrower: $infoBorrower,
            escrowBankName: $escrowBankName ?? null,
            bankNameApproveManual: $bankNameApproveManual ?? null
        );
    }

    public function updateConfidant(string $demandId, User $user, array $data)
    {
        try {
            $apiData = $this->transformData($data);
            $demand = $this->dynamicsCrmClient->getDemandById($demandId, $user);
            return $this->confidantEdit($demand, $apiData, $data['confidant_type']);
        } catch (Exception $e) {
            throw new NotFoundException($e->getMessage());
        }
    }

    public function confidantEdit(array $demand, array $data, $confidantType)
    {
        $jointOwner = $demand['jointOwners'][0];
        if ($demand['customerType']['code'] == 1) {
            if (isset($jointOwner['primaryContact'])) {
                $this->dynamicsCrmClient->putCustomer($jointOwner['primaryContact']['code'], $data);
                $signatoryType = $this->transformSignatoryType($confidantType);
                $this->dynamicsCrmClient->putAccounts($jointOwner['accountId'], $signatoryType);
                return $jointOwner;
            } else {
                throw new BadRequestHttpException('Error: This might not be a legal entity, or jointOwners[0].primaryContact is empty.');
            }
        }
    }

    private function transformSignatoryType($type)
    {
        $signatoryTypeCodeMap = [
            1 => "1", // Директор
            2 => "2", // По доверенности
        ];

        if (!array_key_exists($type, $signatoryTypeCodeMap)) {
            throw new InvalidArgumentException('Недопустимый тип доверенности.');
        }

        $data = [
            'signatoryType' => [
                'code' => $signatoryTypeCodeMap[$type]
            ]
        ];
        return $data;
    }

    private function transformData(array $data): array
    {
        $transformed = [
            'firstName' => $data['first_name'],
            'lastName' => $data['last_name'],
            'middleName' => $data['middle_name'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'birthDate' => $this->formatDate($data['birth_date']),
            'inn' => $data['inn'] ?? null,
            'snils' => $data['snils'] ?? null,
            'documentType' => [
                'code' => $data['is_rus'] === true ? '1' : ($data['is_rus'] === false ? '4' : null),
            ],
        ];

        if (isset($data['gender'])) {
            $transformed['genderCode'] = [
                'code' => $data['gender'] === 'male' ? '1' : ($data['gender'] === 'female' ? '2' : null),
            ];
        }

        return $transformed;
    }

    private function formatDate(string $date): string
    {
        $dateTime = \DateTime::createFromFormat('d.m.Y', $date);
        return $dateTime->format('Y-m-d');
    }

    public function getApprovals($demandId)
    {
        return $this->dynamicsCrmClient->getDemandMortgageApprovals($demandId);
    }

    public function getBankApprovals($approvalId)
    {
        return $this->dynamicsCrmClient->getBankApprovals($approvalId);
    }

    private function compareModifiedOn($a, $b)
    {
        return strtotime($b['modifiedOn']) - strtotime($a['modifiedOn']);
    }
}
