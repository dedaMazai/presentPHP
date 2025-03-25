<?php

namespace App\Services\V2\Sales\Demand\Dto\Deponent;

/**
 * Class LegalEntityDto
 *
 * @package App\Services\V2\Sales\Customer\Dto
 */
class DeponentUrIdDto
{
    public function __construct(
        public string $id,
        public string $joint_owner_id,
        public string $name,
    ) {
    }
}
