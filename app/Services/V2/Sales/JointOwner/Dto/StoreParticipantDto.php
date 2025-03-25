<?php

namespace App\Services\V2\Sales\JointOwner\Dto;

use App\Models\Sales\OwnerType;

/**
 * Class StoreParticipantDto
 *
 * @package App\Services\V2\Sales\JointOwner\Dto
 */
class StoreParticipantDto
{
    public function __construct(
        public OwnerType $ownerType,
        public string $jointownerId,
        public ?string $demandid,
    ) {
    }
}
