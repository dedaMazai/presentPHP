<?php

namespace App\Models\Claim;

use App\Models\Claim\ClaimPass\ClaimPassCar;
use App\Models\Claim\ClaimPass\ClaimPassHuman;
use App\Models\Claim\ClaimPass\ClaimPassStatus;
use App\Models\Claim\ClaimPass\ClaimPassType;
use App\Models\User\User;
use Carbon\Carbon;

/**
 * Class Claim
 *
 * @package App\Models\Claim
 */
class Claim
{
    /**
     * @param string $id
     * @param string|null $number
     * @param ClaimTheme|null $theme
     * @param ClaimStatus $status
     * @param Carbon $createdAt
     * @param Carbon|null $closedAt
     * @param ClaimPaymentStatus|null $paymentStatus
     * @param string|null $comment
     * @param Carbon|null $arrivalDate
     * @param Carbon|null $paymentDate
     * @param int|null $totalPayment
     * @param Carbon|null $scheduledStart
     * @param Carbon|null $scheduledEnd
     * @param User|null $user
     * @param ClaimExecutor[] $executors
     * @param ClaimPassType|null $passType
     * @param ClaimPassCar|null $passCar
     * @param ClaimPassHuman|null $passHuman
     * @param ClaimPassStatus|null $passStatus
     * @param ClaimService[] $services
     * @param ClaimImage[] $images
     * @param string|null $confirmationCode
     * @param string|null $commentQuality
     * @param string|null $rating
     * @param string|null $vendorId
     * @param string|null $vendorName
     * @param string|null $declarantId
     * @param Carbon|null $modifiedOn
     * @param Carbon|null $closedOn
     * @param string|null $accountUkId
     * @param string|null $accountUkServiceSellerId
     * @param int|null $claim_number
     * @param bool|null $is_not_read_sms
     * @param bool|null $is_not_read_document
     */
    public function __construct(
        private string $id,
        private ?string $number,
        private ?ClaimTheme $theme,
        private ClaimStatus $status,
        private Carbon $createdAt,
        private ?Carbon $closedAt,
        private ?ClaimPaymentStatus $paymentStatus,
        private ?string $comment,
        private ?Carbon $arrivalDate,
        private ?Carbon $paymentDate,
        private ?int $totalPayment,
        private ?Carbon $scheduledStart,
        private ?Carbon $scheduledEnd,
        private ?User $user,
        private array $executors,
        private ?ClaimPassType $passType,
        private ?ClaimPassCar $passCar,
        private ?ClaimPassHuman $passHuman,
        private ?ClaimPassStatus $passStatus,
        private array $services,
        private array $images,
        private ?string $confirmationCode,
        private ?string $commentQuality,
        private ?string $rating,
        private ?string $vendorId,
        private ?string $vendorName,
        private ?string $accountNumber,
        private ?string $declarantId = '',
        private ?Carbon $modifiedOn = null,
        private ?Carbon $closedOn = null,
        private ?string $accountUkId = null,
        private ?string $accountUkServiceSellerId = null,
        private ?int $claim_number = null,
        private ?bool $is_not_read_sms = null,
        private ?bool $is_not_read_document = null
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function getTheme(): ?ClaimTheme
    {
        return $this->theme;
    }

    public function getStatus(): ClaimStatus
    {
        return $this->status;
    }

    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }

    public function getClosedAt(): ?Carbon
    {
        return $this->closedAt;
    }

    public function getPaymentStatus(): ?ClaimPaymentStatus
    {
        return $this->paymentStatus;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getArrivalDate(): ?Carbon
    {
        return $this->arrivalDate;
    }

    public function getPaymentDate(): ?Carbon
    {
        return $this->paymentDate;
    }

    public function getTotalPayment(): ?int
    {
        return $this->totalPayment;
    }

    public function getScheduledStart(): ?Carbon
    {
        return $this->scheduledStart;
    }

    public function getScheduledEnd(): ?Carbon
    {
        return $this->scheduledEnd;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @return ClaimExecutor[]
     */
    public function getExecutors(): array
    {
        return $this->executors;
    }

    public function getPassType(): ?ClaimPassType
    {
        return $this->passType;
    }

    public function getPassCar(): ?ClaimPassCar
    {
        return $this->passCar;
    }

    public function getPassHuman(): ?ClaimPassHuman
    {
        return $this->passHuman;
    }

    public function getPassStatus(): ?ClaimPassStatus
    {
        return $this->passStatus;
    }

    /**
     * @return ClaimService[]
     */
    public function getServices(): array
    {
        return $this->services;
    }

    /**
     * @return ClaimImage[]
     */
    public function getImages(): array
    {
        return $this->images;
    }

    public function getConfirmationCode(): ?string
    {
        return $this->confirmationCode;
    }

    public function getCommentQuality(): ?string
    {
        return $this->commentQuality;
    }

    public function getRating(): ?string
    {
        return $this->rating;
    }

    public function getVendorId(): ?string
    {
        return $this->vendorId;
    }

    public function getVendorName(): ?string
    {
        return $this->vendorName;
    }

    public function getModifiedOn(): ?Carbon
    {
        return $this->modifiedOn;
    }

    public function getAccountUkServiceSellerId(): ?string
    {
        return $this->accountUkServiceSellerId;
    }

    public function getAccountUkId(): ?string
    {
        return $this->accountUkId;
    }

    /**
     * @return int
     */
    public function getClaimNumber(): ?int
    {
        return $this->claim_number;
    }

    /**
     * @return bool
     */
    public function getIsNotReadSMS(): ?bool
    {
        return $this->is_not_read_sms;
    }

    public function setReadSMS(): ?bool
    {
        return $this->is_not_read_sms = false;
    }

    /**
     * @return bool
     */
    public function getIsNotReadDocument(): ?bool
    {
        return $this->is_not_read_document;
    }
    public function setReadDocument(): ?bool
    {
        return $this->is_not_read_document = false;
    }

    /**
     * @return Carbon|null
     */
    public function getClosedOn(): ?Carbon
    {
        return $this->closedOn;
    }

    /**
     * @return string|null
     */
    public function getDeclarantId(): ?string
    {
        return $this->declarantId;
    }

    /**
     * @return string|null
     */
    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }
}
