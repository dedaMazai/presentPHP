<?php

namespace App\Services\V2\Sales\Customer\Dto;

use App\Models\Role;
use App\Models\Sales\OwnerType;

/**
 * Class LegalEntityDto
 *
 * @package App\Services\V2\Sales\Customer\Dto
 */
class LegalEntityDto
{
    public function __construct(
        public string $id,
        public string $name,
        public ?int $inn,
        public ?int $ogrn,
        public ?int $kpp,
        public ?string $address_legal,
        public ?string $phone,
        public ?string $mail,
    ) {
    }
}
