<?php

namespace App\Services\Sales\Property\Dto;

use App\Models\Project\Project;
use App\Models\Sales\Property\PropertyStatus;

/**
 * Class PropertyBookingDto
 *
 * @package App\Services\Sales\Property\Dto
 */
class PropertyBookingDto
{
    public function __construct(
        public string $id,
        public ?Project $project,
        public PropertyStatus $status,
        public bool $isEscrow,
    ) {
    }
}
