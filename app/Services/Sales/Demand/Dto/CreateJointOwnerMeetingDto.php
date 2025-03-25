<?php

namespace App\Services\Sales\Demand\Dto;

/**
 * Class CreateJointOwnerMeetingDto
 *
 * @package App\Services\Sales\Demand\Dto
 */
class CreateJointOwnerMeetingDto
{
    /**
     * @param string                 $demandId
     * @param JointOwnerMeetingDto[] $jointOwnerMeetings
     */
    public function __construct(
        public string $demandId,
        public array $jointOwnerMeetings,
    ) {
    }
}
