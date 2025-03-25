<?php

namespace App\Services\V2\Sales\Demand\Dto\Deponent;

/**
 * Class LegalEntityDto
 *
 * @package App\Services\V2\Sales\Customer\Dto
 */
class DeponentFizIdDto
{
    public function __construct(
        public string $id,
        public string $joint_owner_id,
        public string $first_name,
        public string $last_name,
        public string $middle_name,
    ) {
    }
}
