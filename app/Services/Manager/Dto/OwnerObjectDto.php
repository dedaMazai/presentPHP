<?php

namespace App\Services\Manager\Dto;

/**
 * Class OwnerObjectDto
 *
 * @package App\Services\Manager\Dto
 */
class OwnerObjectDto
{
    public function __construct(
        public string $name,
        public string $phone,
        public string $email,
        public string $type,
    ) {
    }
}
