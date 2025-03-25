<?php

namespace App\Services\Sales\Demand\Dto;

/**
 * Class CreateJointOwnerDto
 *
 * @package App\Services\Sales\Demand\Dto
 */
class CreateJointOwnerDto
{
    /**
     * @param string          $demandId
     * @param JointOwnerDto[] $jointOwners
     */
    public function __construct(
        public string $demandId,
        public array $jointOwners,
    ) {
    }
}
