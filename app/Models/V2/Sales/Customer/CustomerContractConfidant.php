<?php

namespace App\Models\V2\Sales\Customer;

use App\Models\Document\DocumentSubtype;
use App\Models\Role;
use App\Models\Sales\Gender;
use App\Models\Sales\OwnerType;
use App\Models\Sales\SignStatus;
use Carbon\Carbon;

/**
 * Class Customer
 *
 * @package App\Models\V2\Sales\Customer
 */
class CustomerContractConfidant
{
    public function __construct(
        private readonly ?string          $id,
        private readonly ?string          $contactId,
        private readonly ?string          $jointOwnerId,
        private readonly ?string          $fullName,
        private readonly ?Gender          $gender,
        private readonly ?string          $phone,
        private readonly ?string          $phoneHome,
        private readonly ?string          $email,
        private readonly ?Carbon          $birthDate,
        private readonly ?string          $birthPlace,
        private readonly ?DocumentSubtype $documentType,
        private readonly ?string          $documentSeries,
        private readonly ?string          $documentNumber,
        private readonly ?string          $documentGiveOut,
        private readonly ?Carbon          $documentDate,
        private readonly ?string          $subdivisionCode,
        private readonly ?string          $addressRegistration,
        private readonly ?string          $citizenship,
        private readonly ?string          $inn,
        private readonly ?string          $snils,
        private readonly ?string          $part,
        private readonly ?Carbon          $esValidityDate,
        private readonly ?SignStatus      $signStatus,
        private readonly ?string          $sdSignatureDocPageUrl,
        private readonly ?string          $sdSignatureAppPageUrl,
        private readonly ?bool            $sdSignatureIsExistDoc,
        private readonly ?bool            $sdSignatureIsExistApp,
        private readonly ?OwnerType       $ownerType,
        private readonly ?string          $ownerTypeComment,
        private readonly ?string          $uin,
        private readonly ?Role            $role,
        private readonly ?bool            $isDepositor,
        private readonly ?bool            $isCustomer,
        private readonly ?string          $label,
        private ?array                    $customerType,
        private ?object                   $legalEntityData = null,
        private ?object                   $signatory = null,
        private ?string                   $lastName = null,
        private ?string                   $firstName = null,
        private ?string                   $middleName = null
    ) {
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getContactId(): ?string
    {
        return $this->contactId;
    }

    public function getJointOwnerId(): ?string
    {
        return $this->jointOwnerId;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function getGender(): ?Gender
    {
        return $this->gender;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getPhoneHome(): ?string
    {
        return $this->phoneHome;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getBirthDate(): ?Carbon
    {
        return $this->birthDate;
    }

    public function getBirthPlace(): ?string
    {
        return $this->birthPlace;
    }

    public function getDocumentType(): ?DocumentSubtype
    {
        return $this->documentType;
    }

    public function getDocumentSeries(): ?string
    {
        return $this->documentSeries;
    }

    public function getDocumentNumber(): ?string
    {
        return $this->documentNumber;
    }

    public function getDocumentGiveOut(): ?string
    {
        return $this->documentGiveOut;
    }

    public function getDocumentDate(): ?Carbon
    {
        return $this->documentDate;
    }

    public function getSubdivisionCode(): ?string
    {
        return $this->subdivisionCode;
    }

    public function getAddressRegistration(): ?string
    {
        return $this->addressRegistration;
    }

    public function getCitizenship(): ?string
    {
        return $this->citizenship;
    }

    public function getInn(): ?string
    {
        return $this->inn;
    }

    public function getSnils(): ?string
    {
        return $this->snils;
    }

    public function getPart(): ?string
    {
        return $this->part;
    }

    public function getEsValidityDate(): ?Carbon
    {
        return $this->esValidityDate;
    }

    public function getSignStatus(): ?SignStatus
    {
        return $this->signStatus;
    }

    public function getSdSignatureDocPageUrl(): ?string
    {
        return $this->sdSignatureDocPageUrl;
    }

    public function getSdSignatureAppPageUrl(): ?string
    {
        return $this->sdSignatureAppPageUrl;
    }

    public function getSdSignatureIsExistDoc(): ?bool
    {
        return $this->sdSignatureIsExistDoc;
    }

    public function getSdSignatureIsExistApp(): ?bool
    {
        return $this->sdSignatureIsExistApp;
    }

    public function getOwnerType(): ?OwnerType
    {
        return $this->ownerType;
    }

    public function getCustomerType(): ?array
    {
        return $this->customerType;
    }

    public function setCustomerType(?array $customerType): void
    {
        $this->customerType = $customerType;
    }

    public function getOwnerTypeComment(): ?string
    {
        return $this->ownerTypeComment;
    }

    public function getUin(): ?string
    {
        return $this->uin;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function getIsDepositor(): ?bool
    {
        return $this->isDepositor;
    }

    public function getIsCustomer(): ?bool
    {
        return $this->isCustomer;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getLegalEntityData(): ?object
    {
        return $this->legalEntityData;
    }

    public function getSignatory(): ?object
    {
        return $this->signatory;
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
}
