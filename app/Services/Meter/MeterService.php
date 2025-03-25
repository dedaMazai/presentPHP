<?php

namespace App\Services\Meter;

use App\Models\Meter\MeterName;
use App\Services\Crm\CrmClient;
use App\Services\Meter\Dto\SaveMeterValueDto;
use Illuminate\Validation\ValidationException;

/**
 * Class MeterService
 *
 * @package App\Services\Meter
 */
class MeterService
{
    public function __construct(private CrmClient $crmClient)
    {
    }

    /**
     * @throws ValidationException
     */
    public function saveMeterValue(string $accountNumber, SaveMeterValueDto $dto): void
    {
        $this->crmClient->saveMeterValue($accountNumber, $dto);
    }

    public function saveMeterName(string $meterId, ?string $name): void
    {
        MeterName::updateOrCreate(['meter_id' => $meterId], ['name' => $name]);
    }
}
