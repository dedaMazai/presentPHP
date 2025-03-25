<?php

namespace App\Services\Meter\Dto;

/**
 * Class MeterValueItemDto
 *
 * @package App\Services\Meter\Dto
 */
class MeterValueItemDto
{
    public function __construct(
        public string $tariffId,
        public ?float $currentValue,
    ) {
    }
}
