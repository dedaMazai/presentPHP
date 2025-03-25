<?php

namespace App\Models\V2\Sales\Demand;

use App\Models\Contract\Contract;
use App\Models\V2\Sales\ArticleOrder;
use App\Models\V2\Sales\CharacteristicSale\CharacteristicSale;
use App\Models\V2\Sales\Customer\Customer;
use App\Models\Sales\Deal;
use App\Models\Sales\FamilyStatus;
use App\Models\Sales\Owner;
use App\Models\Sales\Ownership;
use App\Models\Sales\OwnerType;
use App\Models\Sales\PaymentMode;
use App\Models\Sales\PaymentPlan;
use App\Models\V2\Sales\Property\Property;
use App\Models\Sales\TradeIn;
use App\Models\V2\Sales\LetterOfCreditBank;
use Carbon\Carbon;

/**
 * Class Demand
 *
 * @package App\Models\Sales\Demand
 */
class Demand
{
    /**
     * @param string $id
     * @param string|null $parentId
     * @param string|null $number
     * @param DemandType $type
     * @param Carbon|null $createdDate
     * @param DemandBookingType|null $bookingType
     * @param string|null $stepName
     * @param Carbon|null $beginDate
     * @param Carbon|null $endDate
     * @param string|null $subject
     * @param string|null $lastName
     * @param string|null $firstName
     * @param string|null $middleName
     * @param Carbon|null $birthDate
     * @param string|null $phone
     * @param string|null $email
     * @param string|null $addressId
     * @param Property|null $property
     * @param Contract|null $contract
     * @param Customer|null $customer
     * @param Customer[] $jointOwners
     * @param ArticleOrder[] $articleOrders
     * @param PaymentPlan[] $paymentPlans
     * @param Owner|null $owner
     * @param string|null $description
     * @param DemandState|null $state
     * @param DemandStatus|null $status
     * @param string|null $reserveOpportunityName
     * @param DemandBookingStatus|null $bookingPaymentStatus
     * @param bool|null $isLetterOfCredit
     * @param bool|null $isElectronicRegistration
     * @param string|null $hypothecBankId
     * @param string|null $letterOfCreditBankId
     * @param PaymentMode|null $paymentMode
     * @param Carbon|null $finishingSalesStart
     * @param Carbon|null $finishingSalesStop
     * @param FamilyStatus|null $familyStatus
     * @param Carbon $modifiedOn
     * @param Contract[] $propertyContracts
     * @param CharacteristicSale[] $characteristics
     * @param CharacteristicSale|null $baseFinishVariant
     * @param ArticleOrder|null $mainArticleOrder
     * @param Contract|null $paidBookingContract
     * @param Deal|null $deal
     * @param Demand|null $mortgageDemand
     * @param array|null $bindedCharacteristicSales
     * @param string|null $articlePrice
     * @param string|null $sumOpportunityMinusDiscount
     * @param string|null $demandMainId
     * @param string|null $depositorFizId
     * @param array|null $paymentPlan
     * @param string|null $articleId
     */
    public function __construct(
        private string $id,
        private ?string $parentId,
        private ?string $number,
        private DemandType $type,
        private ?Carbon $createdDate,
        private ?DemandBookingType $bookingType,
        private ?string $stepName,
        private ?Carbon $beginDate,
        private ?Carbon $endDate,
        private ?string $subject,
        private ?string $lastName,
        private ?string $firstName,
        private ?string $middleName,
        private ?Carbon $birthDate,
        private ?string $phone,
        private ?string $email,
        private ?string $addressId,
        private ?Property $property,
        private ?Contract $contract,
        private ?Customer $customer,
        private array $jointOwners,
        private array $articleOrders,
        private array $paymentPlans,
        private ?Owner $owner,
        private ?string $description,
        private ?DemandState $state,
        private ?DemandStatus $status,
        private ?string $reserveOpportunityName,
        private ?DemandBookingStatus $bookingPaymentStatus,
        private ?bool $isLetterOfCredit,
        private ?bool $isElectronicRegistration,
        private ?string $hypothecBankId,
        private ?string $letterOfCreditBankId,
        private ?PaymentMode $paymentMode,
        private ?Carbon $finishingSalesStart,
        private ?Carbon $finishingSalesStop,
        private ?FamilyStatus $familyStatus,
        private Carbon $modifiedOn,
        private array $propertyContracts,
        private array $characteristics,
        private ?CharacteristicSale $baseFinishVariant,
        private ?ArticleOrder $mainArticleOrder,
        private ?Contract $paidBookingContract,
        private ?Deal $deal,
        private ?Demand $mortgageDemand,
        private ?array $bindedCharacteristicSales,
        private ?string $articlePrice,
        private ?string $sumOpportunityMinusDiscount,
        private ?string $demandMainId,
        private ?string $depositorFizId,
        private ?array $paymentPlan,
        private ?string $articleId,
        private ?string $nextStepBankInfo,
        private ?bool $isDigitalTransaction,
        private ?Ownership $ownership,
        private ?TradeIn $tradeIn,
        private ?bool $isBookingCancelAvailable,
        private ?bool $isMortgageAvailable,
        private ?bool $isMortgageOnlineAvailable,
        private ?string $mortgageOnlineInfo,
        private ?bool $isEarlyPreviouslyOwners,
        private ?bool $isAvailableChangePaymentMode,
        private ?string $changePaymentModeDisableInfo,
        private ?string $instalmentsInfo,
        private ?bool $isAvailibleInstalments,
        private ?bool $isPaymentBookingAvailible,
        private ?string $nextStepsBanksInfo,
        private ?array $borrowers,
        private ?bool $isBorrowersAvailible,
        private ?array $paymentBooking,
        private ?array $instalments,
        private ?LetterOfCreditBank $letterOfCreditBank,
        private ?bool $isTransactionTermsFull,
        private ?string $transactionTermsFullInfo,
        private ?array $stages,
        private ?ArticleOrder $salesScheme,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function getType(): DemandType
    {
        return $this->type;
    }

    public function getCreatedDate(): ?Carbon
    {
        return $this->createdDate;
    }

    public function getBookingType(): ?DemandBookingType
    {
        return $this->bookingType;
    }

    public function getStepName(): ?string
    {
        return $this->stepName;
    }

    public function getBeginDate(): ?Carbon
    {
        return $this->beginDate;
    }

    public function getEndDate(): ?Carbon
    {
        return $this->endDate;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function getBirthDate(): ?Carbon
    {
        return $this->birthDate;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getAddressId(): ?string
    {
        return $this->addressId;
    }

    public function getProperty(): ?Property
    {
        return $this->property;
    }

    public function getContract(): ?Contract
    {
        return $this->contract;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    /**
     * @return Customer[]
     */
    public function getJointOwners(): array
    {
        return $this->jointOwners;
    }

    /**
     * @return ArticleOrder[]
     */
    public function getArticleOrders(): array
    {
        return $this->articleOrders;
    }

    /**
     * @return PaymentPlan[]
     */
    public function getPaymentPlans(): array
    {
        return $this->paymentPlans;
    }

    public function getOwner(): ?Owner
    {
        return $this->owner;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getState(): ?DemandState
    {
        return $this->state;
    }

    public function getStatus(): ?DemandStatus
    {
        return $this->status;
    }

    public function getReserveOpportunityName(): ?string
    {
        return $this->reserveOpportunityName;
    }

    public function getBookingPaymentStatus(): ?DemandBookingStatus
    {
        return $this->bookingPaymentStatus;
    }

    public function getIsLetterOfCredit(): ?bool
    {
        return $this->isLetterOfCredit;
    }

    public function getIsElectronicRegistration(): ?bool
    {
        return $this->isElectronicRegistration;
    }

    public function getHypothecBankId(): ?string
    {
        return $this->hypothecBankId;
    }

    public function getLetterOfCreditBankId(): ?string
    {
        return $this->letterOfCreditBankId;
    }

    public function getPaymentMode(): ?PaymentMode
    {
        return $this->paymentMode;
    }

    public function getFinishingSalesStart(): ?Carbon
    {
        return $this->finishingSalesStart;
    }

    public function getFinishingSalesStop(): ?Carbon
    {
        return $this->finishingSalesStop;
    }

    public function getFamilyStatus(): ?FamilyStatus
    {
        return $this->familyStatus;
    }

    public function getModifiedOn(): Carbon
    {
        return $this->modifiedOn;
    }

    /**
     * @return Contract[]
     */
    public function getPropertyContracts(): array
    {
        return $this->propertyContracts;
    }

    /**
     * @return CharacteristicSale[]
     */
    public function getCharacteristics(): array
    {
        return $this->characteristics;
    }

    public function getBaseFinishVariant(): ?CharacteristicSale
    {
        return $this->baseFinishVariant;
    }

    public function getMainArticleOrder(): ?ArticleOrder
    {
        return $this->mainArticleOrder;
    }

    public function getPaidBookingContract(): ?Contract
    {
        return $this->paidBookingContract;
    }

    public function getOwnerType(): ?OwnerType
    {
        return isset($this->getJointOwners()[0]) ? $this->getJointOwners()[0]->getOwnerType() : null;
    }

    public function isFinishingAvailable(): bool
    {
        if ($this->getFinishingSalesStart() && $this->getFinishingSalesStop()) {
            return Carbon::today()->betweenIncluded($this->getFinishingSalesStart(), $this->getFinishingSalesStop());
        }

        return false;
    }

    public function getDeal(): ?Deal
    {
        return $this->deal;
    }

    public function getMortgageDemand(): ?Demand
    {
        return $this->mortgageDemand;
    }

    public function getBindedCharacteristicSales(): ?array
    {
        return $this->bindedCharacteristicSales;
    }

    public function getArticlePrice(): ?string
    {
        return $this->articlePrice;
    }

    public function getSumOpportunityMinusDiscount(): ?string
    {
        return $this->sumOpportunityMinusDiscount;
    }

    public function getDemandMainId(): ?string
    {
        return $this->demandMainId;
    }

    public function getDepositorFizId(): ?string
    {
        return $this->depositorFizId;
    }

    public function getPaymentPlan(): ?array
    {
        return $this->paymentPlan;
    }

    public function getArticleId(): ?string
    {
        return $this->articleId;
    }

    public function getNextStepBankInfo(): ?string
    {
        return $this->nextStepBankInfo;
    }

    public function getIsDigitalTransaction(): ?bool
    {
        return $this->isDigitalTransaction;
    }

    public function getOwnership(): ?Ownership
    {
        return $this->ownership;
    }

    public function getTradeIn(): ?TradeIn
    {
        return $this->tradeIn;
    }

    public function getIsBookingCancelAvailable(): ?bool
    {
        return $this->isBookingCancelAvailable;
    }

    public function getIsMortgageAvailable(): ?bool
    {
        return $this->isMortgageAvailable;
    }

    public function getIsMortgageOnlineAvailable(): ?bool
    {
        return $this->isMortgageOnlineAvailable;
    }

    public function getMortgageOnlineInfo(): ?string
    {
        return $this->mortgageOnlineInfo;
    }

    public function getIsEarlyPreviouslyOwners(): ?bool
    {
        return $this->isEarlyPreviouslyOwners;
    }

    public function getIsAvailableChangePaymentMode(): ?bool
    {
        return $this->isAvailableChangePaymentMode;
    }

    public function getChangePaymentModeDisableInfo(): ?string
    {
        return $this->changePaymentModeDisableInfo;
    }

    public function getInstalmentsInfo(): ?string
    {
        return $this->instalmentsInfo;
    }

    public function getIsAvailibleInstalments(): ?bool
    {
        return $this->isAvailibleInstalments;
    }

    public function getIsPaymentBookingAvailible(): ?bool
    {
        return $this->isPaymentBookingAvailible;
    }

    public function getNextStepsBanksInfo(): ?string
    {
        return $this->nextStepsBanksInfo;
    }

    public function getBorrowers(): ?array
    {
        return $this->borrowers;
    }

    public function getIsBorrowersAvailible(): ?bool
    {
        return $this->isBorrowersAvailible;
    }

    public function getPaymentBooking(): ?array
    {
        return $this->paymentBooking;
    }

    public function getInstalments(): ?array
    {
        return $this->instalments;
    }

    public function getLetterOfCreditBank(): ?LetterOfCreditBank
    {
        return $this->letterOfCreditBank;
    }

    public function getIsTransactionTermsFull(): ?bool
    {
        return $this->isTransactionTermsFull;
    }

    public function getTransactionTermsFullInfo(): ?string
    {
        return $this->transactionTermsFullInfo;
    }

    public function getStages(): ?array
    {
        return $this->stages;
    }

    /**
     * @return ArticleOrder|null
     */
    public function getSalesScheme(): ?ArticleOrder
    {
        return $this->salesScheme;
    }
}
