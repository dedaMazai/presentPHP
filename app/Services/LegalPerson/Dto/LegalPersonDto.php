<?php

namespace App\Services\LegalPerson\Dto;

/**
 * Class LegalPersonDto
 *
 * @package App\Services\LegalPerson\Dto
 */
class LegalPersonDto
{
    public function __construct(
        public string $id,
        public ?string $name,
        public ?string $inn,
        public ?string $ogrn,
        public ?string $kpp,
        public ?string $address_legal,
        public ?string $phone,
        public ?string $mail,
        public ?string $account_type,
        public ?string $message,
        public ?string $type_message
    ) {
    }
}
