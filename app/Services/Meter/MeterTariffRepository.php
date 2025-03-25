<?php

namespace App\Services\Meter;

use App\Models\Meter\MeterTariff;
use App\Models\Meter\MeterTariffValue;
use App\Services\Crm\CrmClient;
use Illuminate\Validation\ValidationException;

/**
 * Class MeterTariffRepository
 *
 * @package App\Services\Meter
 */
class MeterTariffRepository
{
    public function __construct(private CrmClient $crmClient)
    {
    }

    /**
     * @return MeterTariff[]
     * @throws ValidationException
     */
    public function getAllByAccountNumber(string $accountNumber): array
    {
        $data = $this->crmClient->getMeterTariffs($accountNumber);

        $meterTariffs = [];
        foreach ($data as $rawMeterTariff) {
            if (!isset($meterTariffs[$rawMeterTariff['tariff_name']])) {
                $meterTariffs[$rawMeterTariff['tariff_name']] = $rawMeterTariff;
            }
            $meterTariffs[$rawMeterTariff['tariff_name']]['values'][] = $rawMeterTariff;
        }

        return array_map(fn($meterTariff) => $this->makeMeterTariff($meterTariff), $meterTariffs);
    }

    private function makeMeterTariff(array $data): MeterTariff
    {
        $values = [];
        foreach ($data['values'] as $value) {
            $values[] = $this->makeMeterTariffValue($value);
        }

        return new MeterTariff(
            type: $data['type'],
            name: $data['tariff_name'] ?? null,
            unit: $data['unit'],
            values: $values,
        );
    }

    private function makeMeterTariffValue(array $data): MeterTariffValue
    {
        return new MeterTariffValue(
            subtype: $data['type_tariff'],
            cost: $data['tariff'],
        );
    }
}
