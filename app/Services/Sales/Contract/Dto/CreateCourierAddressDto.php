<?php

namespace App\Services\Sales\Contract\Dto;

/**
 * Class CreateCourierAddressDto
 *
 * @package App\Services\Sales\Contract\Dto
 */
class CreateCourierAddressDto
{
    public function __construct(
        public string $jointOwnerId,
        public string $id,
        public string $city,
        public string $address,
        public ?string $description,
    ) {
    }
}
