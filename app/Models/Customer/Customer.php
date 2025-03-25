<?php

namespace App\Models\Customer;

use App\Models\Document\DocumentType;
use Carbon\Carbon;

/**
 * Class Customer
 *
 * @package App\Models\Customer
 */
class Customer
{
    public function __construct(
        private string $id,
        private string $lastName,
        private string $firstName,
        private ?string $middleName,
        private string $phone,
        private ?string $email,
        private Carbon $birthDate,
        private ?DocumentType $documentType = null,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
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

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getBirthDate(): Carbon
    {
        return $this->birthDate;
    }

    public function getDocumentType(): ?DocumentType
    {
        return $this->documentType;
    }
}
