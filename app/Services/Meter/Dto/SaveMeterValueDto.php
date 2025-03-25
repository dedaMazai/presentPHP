<?php

namespace App\Services\Meter\Dto;

/**
 * Class SaveMeterValueDto
 *
 * @package App\Services\Meter\Dto
 */
class SaveMeterValueDto
{
    /**
     * @param string              $id
     * @param MeterValueItemDto[] $values
     */
    public function __construct(
        public string $id,
        public array $values,
    ) {
    }
}
