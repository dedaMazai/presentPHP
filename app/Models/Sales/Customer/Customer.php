<?php

namespace App\Models\Sales\Customer;

use App\Models\Document\DocumentSubtype;
use App\Models\Role;
use App\Models\Sales\Gender;
use App\Models\Sales\OwnerType;
use App\Models\Sales\SignStatus;
use Carbon\Carbon;

/**
 * Class Customer
 *
 * @package App\Models\Sales\Customer
 */
class Customer
{
    public function __construct(
        private string $id,
        private ?string $contactId,
        private ?string $jointOwnerId,
        private string $lastName,
        private string $firstName,
        private ?string $middleName,
        private ?Gender $gender,
        private ?string $phone,
        private ?string $phoneHome,
        private ?string $email,
        private Carbon $birthDate,
        private ?string $birthPlace,
        private ?DocumentSubtype $documentType,
        private ?string $documentSeries,
        private ?string $documentNumber,
        private ?string $documentGiveOut,
        private ?Carbon $documentDate,
        private ?string $subdivisionCode,
        private ?string $addressRegistration,
        private ?string $citizenship,
        private ?string $inn,
        private ?string $snils,
        private ?string $part,
        private ?Carbon $esValidityDate,
        private ?SignStatus $signStatus,
        private ?string $sdSignatureDocPageUrl,
        private ?string $sdSignatureAppPageUrl,
        private ?bool $sdSignatureIsExistDoc,
        private ?bool $sdSignatureIsExistApp,
        private ?OwnerType $ownerType,
        private ?string $ownerTypeComment,
        private ?string $uin,
        private ?Role $role,
        private ?int $married,
        private ?string $label,
        private ?string $name = null,
    ) {
    }

    public function getId(): string
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
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

    public function getBirthDate(): Carbon
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

    public function getMarried(): ?int
    {
        return $this->married;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getFullName(): ?string
    {
        return $this->lastName . ' ' . $this->firstName . ' ' . $this->middleName;
    }
}
