<?php

namespace App\Services\V2\Sales\Property\Dto;

use App\Models\Project\Project;
use App\Models\V2\Sales\Property\PropertyStatus;

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
