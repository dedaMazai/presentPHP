<?php

namespace App\Services\Meter;

use App\Models\Meter\Meter;
use App\Models\Meter\MeterDataInputType;
use App\Models\Meter\MeterName;
use App\Models\Meter\MeterSubtype;
use App\Models\Meter\MeterType;
use App\Models\Meter\MeterValue;
use App\Services\Crm\CrmClient;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

/**
 * Class MeterRepository
 *
 * @package App\Services\Meter
 */
class MeterRepository
{
    public function __construct(private CrmClient $crmClient)
    {
    }

    /**
     * @return Meter[]
     * @throws ValidationException
     */
    public function getAllByAccountNumber(
        string $accountNumber,
        ?MeterType $type = null,
        ?MeterSubtype $subtype = null,
    ): array {
        $data = $this->crmClient->getMeters($accountNumber, $type, $subtype);

        $meters = [];
        foreach ($data as $rawMeter) {
            $meter = $this->makeMeter($rawMeter);

            if ($type) {
                if (!$type->equals($meter->getType())) {
                    continue;
                }

                if ($subtype && !$subtype->equals($meter->getSubtype())) {
                    continue;
                }
            }

            $meters[] = $meter;
        }

        return $meters;
    }

    private function makeMeter(array $data): Meter
    {
        $values = [];
        if (isset($data['values'])) {
            foreach ($data['values'] as $value) {
                $values[] = $this->makeMeterValue($value);
            }
        }

        return new Meter(
            id: $data['id'],
            type: MeterType::from($data['type']),
            subtype: isset($data['subtype']) ? MeterSubtype::tryFrom($data['subtype']) : null,
            dataInputType: MeterDataInputType::from($data['data_input_type'] ?? 'manual'),
            number: $data['number'],
            isPreviousValueCalculatedByStandard: $data['is_previous_value_calculated_by_standard'] ?? null,
            isValuesEnteredInCurrentPeriod: $data['is_values_entered_in_current_period'] ?? null,
            dateVerification: isset($data['date_verification']) && $data['date_verification'] ?
                new Carbon($data['date_verification']) : null,
            values: $values,
            name: MeterName::firstWhere(['meter_id' => $data['id']]),
        );
    }

    private function makeMeterValue(array $data): MeterValue
    {
        return new MeterValue(
            tariffId: $data['tariff_id'],
            tariffName: $data['tafiff_name'],
            currentValue: $data['current_value'] ?? null,
            previousValue: $data['previous_value'],
        );
    }
}
