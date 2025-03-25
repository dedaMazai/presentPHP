<?php

namespace App\Services\Contract\Dto;

/**
 * Class ContractJointOwnerDto
 *
 * @package App\Services\Claim\Dto
 */
class ContractJointOwnerDto
{
    public function __construct(
        public ?string $courierSignInfo,
        public ?bool $isCommonInfoSignMeAvailable,
        public ?string $reissueGuideUrl,
        public ?array $jointOwners,
    ) {
    }
}
