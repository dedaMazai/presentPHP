<?php

namespace App\Services\Sales\IndividualOwner\Dto;

use App\Models\Sales\Gender;
use Carbon\Carbon;

/**
 * Class IndividualOwnerDto
 *
 * @package App\Services\Sales\IndividualOwner\Dto
 */
class IndividualOwnerDto
{
    public function __construct(
        public string $lastName,
        public string $firstName,
        public ?string $middleName,
        public ?Gender $gender,
        public ?string $phone,
        public ?string $phoneHome,
        public ?string $email,
        public Carbon $birthDate,
        public ?string $inn,
        public ?string $snils,
        public ?int $married,
        public ?bool $is_rus,
        public ?array $documents
    ) {
    }
}
