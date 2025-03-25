<?php

namespace App\Models\IndividualOwner;

use App\Models\Sales\Gender;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class IndividualOwner
 *
 * @package App\Models\IndividualOnwer
 */
class IndividualOwner extends Model
{
    public function __construct(
        private string $lastName,
        private string $firstName,
        private ?string $middleName,
        private ?Gender $gender,
        private ?string $phone,
        private ?string $phoneHome,
        private ?string $email,
        private Carbon $birthDate,
        private ?string $inn,
        private ?string $snils,
        private ?int $married,
        private ?bool $is_rus,
        private ?array $documents
    ) {
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

    public function getInn(): ?string
    {
        return $this->inn;
    }

    public function getSnils(): ?string
    {
        return $this->snils;
    }

    public function getMarried(): int
    {
        return $this->married;
    }

    public function isRus(): bool
    {
        return $this->is_rus;
    }

    public function getDocument(): array
    {
        return $this->documents;
    }
}
