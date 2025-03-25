<?php

namespace App\Services\Sales\JointOwner\Dto;

use App\Models\Sales\OwnerType;

/**
 * Class StoreParticipantDto
 *
 * @package App\Services\Sales\JointOwner\Dto
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
