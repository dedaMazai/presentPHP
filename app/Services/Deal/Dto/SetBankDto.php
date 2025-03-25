<?php

namespace App\Services\Deal\Dto;

use App\Models\Sales\Bank\DemandBankType;

/**
 * Class SetBankDto
 *
 * @package App\Services\Deal\Dto
 */
class SetBankDto
{
    public function __construct(
        public string $demandId,
        public string $bankId,
        public DemandBankType $bankType,
        public ?bool $isSberClient = null
    ) {
    }
}
