<?php

namespace App\Services\User\Dto;

use Carbon\Carbon;

/**
 * Class CreateUserDto
 *
 * @package App\Services\User\Dto
 */
class CreateUserDto
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public ?string $middleName,
        public string $phone,
        public string $email,
        public Carbon $birthDate,
    ) {
    }
}
