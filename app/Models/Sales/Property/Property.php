<?php

namespace App\Models\Sales\Property;

use App\Models\Contract\Contract;
use App\Models\Project\Project;
use App\Models\Sales\Address\Address;
use App\Models\Sales\CharacteristicSale\CharacteristicSale;
use Carbon\Carbon;

/**
 * Class Property
 *
 * @package App\Models\Sales\Property
 */
class Property
{
    /**
     * @param string $id
     * @param string $code
     * @param Address $address
     * @param Project|null $project
     * @param PropertyStatus|null $status
     * @param string $number
     * @param string $layoutId
     * @param string $layoutNumber
     * @param int $floor
     * @param int|null $platformNumber
     * @param int $rooms
     * @param float|null $quantity
     * @param float|null $spaceBtiWoBalcony
     * @param float|null $spaceBti
     * @param float|null $spaceDeviation
     * @param float|null $payDeviation
     * @param float|null $cost
     * @param float|null $price
     * @param string|null $unitCode
     * @param Carbon|null $bookingEndAt
     * @param PropertyType|null $type
     * @param string|null $subtype
     * @param PropertyImage|null $plan
     * @param PropertyVariant|null $variant
     * @param PropertyImage[] $images
     * @param Contract[] $contracts
     * @param bool $isEscrow
     * @param bool $isReservationPaid
     * @param string|null $escrowBankId
     * @param string|null $escrowBankName
     * @param PropertyReserveDuration|null $reserveDuration
     * @param PropertyReserveType|null $reserveType
     * @param float|null $sumDiscount
     * @param bool $isReadyForTransfer
     * @param CharacteristicSale[] $instalments
     * @param CharacteristicSale[] $finishes
     * @param string|null $url
     * @param string|null $addressPost
     * @param float|null $priceDiscount
     * @param string|null $planObject
     * @param array|null $freeBookingPeriod
     */
    public function __construct(
        private string $id,
        private string $code,
        private Address $address,
        private ?Project $project,
        private ?PropertyStatus $status,
        private string $number,
        private string $layoutId,
        private string $layoutNumber,
        private int $floor,
        private ?int $platformNumber,
        private int $rooms,
        private ?float $quantity,
        private ?float $spaceBtiWoBalcony,
        private ?float $spaceBti,
        private ?float $spaceDeviation,
        private ?float $payDeviation,
        private ?float $cost,
        private ?float $price,
        private ?string $unitCode,
        private ?Carbon $bookingEndAt,
        private ?PropertyType $type,
        private ?string $subtype,
        private ?PropertyImage $plan,
        private ?PropertyVariant $variant,
        private array $images,
        private array $contracts,
        private ?bool $isEscrow,
        private ?bool $isReservationPaid,
        private ?string $escrowBankId,
        private ?string $escrowBankName,
        private ?PropertyReserveDuration $reserveDuration,
        private ?PropertyReserveType $reserveType,
        private ?float $sumDiscount,
        private ?bool $isReadyForTransfer,
        private array $instalments,
        private array $finishes,
        private ?float $priceDiscount,
        private ?string $planObject,
        private ?array $freeBookingPeriod,
        private ?string $nameLk,
        private ?bool $isBookingAvailible,
        private ?ObjectPlan $plans,
        private ?string $articleVariantTm1Code,
        private ?Carbon $finishingSalesStart,
        private ?Carbon $finishingSalesStop,
        private ?string $url = '',
        private ?string $zid = '',
        private ?string $addressPost = '',
        private ?array $letterOfCreditBanks = [],
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function getStatus(): ?PropertyStatus
    {
        return $this->status;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getLayoutId(): string
    {
        return $this->layoutId;
    }

    public function getLayoutNumber(): string
    {
        return $this->layoutNumber;
    }

    public function getFloor(): int
    {
        return $this->floor;
    }

    public function getPlatformNumber(): ?int
    {
        return $this->platformNumber;
    }

    public function getRooms(): int
    {
        return $this->rooms;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function getSpaceBtiWoBalcony(): ?float
    {
        return $this->spaceBtiWoBalcony;
    }

    public function getSpaceBti(): ?float
    {
        return $this->spaceBti;
    }

    public function getSpaceDeviation(): ?float
    {
        return $this->spaceDeviation;
    }

    public function getPayDeviation(): ?float
    {
        return $this->payDeviation;
    }

    public function getCost(): ?float
    {
        return $this->cost;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function getUnitCode(): ?string
    {
        return $this->unitCode;
    }

    public function getBookingEndAt(): ?Carbon
    {
        return $this->bookingEndAt;
    }

    public function getType(): ?PropertyType
    {
        return $this->type;
    }

    public function getSubtype(): ?string
    {
        return $this->subtype;
    }

    public function getPlan(): ?PropertyImage
    {
        return $this->plan;
    }

    public function getVariant(): ?PropertyVariant
    {
        return $this->variant;
    }

    /**
     * @return PropertyImage[]
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @return Contract[]
     */
    public function getContracts(): array
    {
        return $this->contracts;
    }

    public function getIsEscrow(): ?bool
    {
        return $this->isEscrow;
    }

    public function getIsReservationPaid(): ?bool
    {
        return $this->isReservationPaid;
    }

    public function getEscrowBankId(): ?string
    {
        return $this->escrowBankId;
    }

    public function getEscrowBankName(): ?string
    {
        return $this->escrowBankName;
    }

    public function getReserveDuration(): ?PropertyReserveDuration
    {
        return $this->reserveDuration;
    }

    public function getReserveType(): ?PropertyReserveType
    {
        return $this->reserveType;
    }

    public function getSumDiscount(): ?float
    {
        return $this->sumDiscount;
    }

    public function getIsReadyForTransfer(): ?bool
    {
        return $this->isReadyForTransfer;
    }

    /**
     * @return CharacteristicSale[]
     */
    public function getInstalments(): array
    {
        return $this->instalments;
    }

    /**
     * @return CharacteristicSale[]
     */
    public function getFinishes(): array
    {
        return $this->finishes;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     */
    public function setUrl(?string $url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddressPost(): ?string
    {
        return $this->addressPost;
    }

    /**
     * @return float|null
     */
    public function getPriceDiscount(): ?float
    {
        return $this->priceDiscount;
    }

    /**
     * @return string|null
     */
    public function getNameLk(): ?string
    {
        return $this->nameLk;
    }

    /**
     * @return bool|null
     */
    public function getIsBookingAvailible(): ?bool
    {
        return $this->isBookingAvailible;
    }

    /**
     * @return string|null
     */
    public function getPlanObject(): ?string
    {
        return $this->planObject;
    }

    /**
     * @return array|null
     */
    public function getFreeBookingPeriod(): ?array
    {
        return $this->freeBookingPeriod;
    }

    /**
     * @return ObjectPlan|null
     */
    public function getPlans(): ?ObjectPlan
    {
        return $this->plans;
    }

    /**
     * @return string|null
     */
    public function getArticleVariantTm1Code(): ?string
    {
        return $this->articleVariantTm1Code;
    }

    /**
     * @return Carbon|null
     */
    public function getFinishingSalesStart(): ?Carbon
    {
        return $this->finishingSalesStart;
    }

    /**
     * @return Carbon|null
     */
    public function getFinishingSalesStop(): ?Carbon
    {
        return $this->finishingSalesStop;
    }

    public function getLetterOfCreditBanks(): ?array
    {
        return $this->letterOfCreditBanks;
    }

    /**
     * @return string|null
     */
    public function getZid(): ?string
    {
        return $this->zid;
    }
}
