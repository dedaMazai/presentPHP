<?php

namespace App\Services\Pass\Dto;

use App\Models\Pass\PassAssignment;
use App\Models\Pass\PassCarType;
use App\Models\Pass\PassType;
use Carbon\Carbon;

/**
 * Class SavePassDto
 *
 * @package App\Services\Pass\Dto
 */
class SavePassDto
{
    /**
     * @param PassType $passType
     * @param PassAssignment $assignment
     * @param PassCarType|null $carType
     * @param string|null $carNumber
     * @param string|null $name
     * @param Carbon|null $arrivalDate
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @param array|null $comment
     */
    public function __construct(
        public PassType $passType,
        public PassAssignment $assignment,
        public ?PassCarType $carType,
        public ?string $carNumber,
        public ?string $name,
        public ?Carbon $arrivalDate,
        public ?Carbon $startDate,
        public ?Carbon $endDate,
        public ?string $comment,
    ) {
    }
}
