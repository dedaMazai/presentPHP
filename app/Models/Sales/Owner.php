<?php

namespace App\Models\Sales;

/**
 * Class Owner
 *
 * @package App\Models\Sales
 */
class Owner
{
    public function __construct(
        private string $id,
        private string $lastName,
        private string $firstName,
        private ?string $middleName,
        private ?string $phone,
        private ?string $email,
        private ?string $passwordHash,
        private ?string $dhEmail,
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function getDhEmail(): ?string
    {
        return $this->dhEmail;
    }
}
