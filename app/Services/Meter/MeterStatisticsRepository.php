<?php

namespace App\Services\Meter;

use App\Models\Meter\MeterStatistics;
use App\Models\Meter\MeterStatisticsValue;
use App\Models\Meter\MeterSubtype;
use App\Models\Meter\MeterType;
use App\Services\Crm\CrmClient;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use RuntimeException;

/**
 * Class MeterStatisticsRepository
 *
 * @package App\Services\Meter
 */
class MeterStatisticsRepository
{
    public function __construct(private CrmClient $crmClient)
    {
    }

    /**
     * @throws ValidationException
     */
    public function getByAccountNumberAndType(
        string $accountNumber,
        MeterType $type,
        ?MeterSubtype $subtype,
        Carbon $startDate,
        Carbon $endDate,
    ): MeterStatistics {
        $data = $this->crmClient->getMeterStatistics($accountNumber, $type, $subtype, $startDate, $endDate);

        if ($type->equals(MeterType::water()) && !$subtype) {
            $preparedData = $this->prepareWaterStatistics($data);
        } elseif (!isset($data[0])) {
            return new MeterStatistics($type, $subtype, null, 0, 0, []);
        }

        return $this->makeMeterStatistics($type, $subtype, $preparedData ?? $data);
    }

    private function prepareWaterStatistics(array $data): array
    {
        $preparedData = [
            'type' => MeterType::water()->value,
            'statistics' => [],
        ];

        foreach ($data as $subTypeData) {
            $preparedData['unit'] = $subTypeData['unit'];
            $tariff = MeterSubtype::from($subTypeData['subtype'])->label;

            foreach ($subTypeData['statistics'] as $statistic) {
                $preparedData['statistics'][$statistic['date']]['date'] = $statistic['date'];
                if (isset($preparedData['statistics'][$statistic['date']][$tariff])) {
                    $preparedData['statistics'][$statistic['date']][$tariff] += $statistic['dT3'];
                } else {
                    $preparedData['statistics'][$statistic['date']][$tariff] = $statistic['dT3'];
                }
            }
        }

        return [$preparedData];
    }

    private function makeMeterStatistics(MeterType $type, ?MeterSubtype $subtype, array $data): MeterStatistics
    {
        $statistics = $this->makeMeterStatisticsValue($type, $subtype, $data);

        $total = 0;
        $average = 0;
        $averageCnt = 0;
        foreach ($statistics as $statistic) {
            $total += $statistic->getTotal();
            $average += $statistic->getAverage();
            $averageCnt++;
        }
        $average = $average / $averageCnt;

        return new MeterStatistics(
            type: $type,
            subtype: $subtype,
            unit: $data[0]['unit'] ?? null,
            total: $total,
            average: $average,
            statistics: $statistics,
        );
    }

    /**
     * @return MeterStatisticsValue[]
     */
    private function makeMeterStatisticsValue(MeterType $type, ?MeterSubtype $subtype, array $data): array
    {
        if ($type->equals(MeterType::water()) && !$subtype) {
            $statistics = [
                MeterSubtype::cold()->label => [],
                MeterSubtype::hot()->label => [],
                MeterSubtype::pure()->label => [],
            ];
        } elseif ($type->equals(MeterType::water())) {
            $statistics = [
                'T3' => [],
            ];
        } elseif ($type->equals(MeterType::electricity())) {
            $statistics = [
                'T1' => [],
                'T2' => [],
                'T3' => [],
            ];
        } elseif ($type->equals(MeterType::heat())) {
            $statistics = [
                'T3' => [],
            ];
        } else {
            throw new RuntimeException('Unable to determine meters type.');
        }

        foreach ($statistics as $tariff => $tariffStatistics) {
            $total = 0;

            $tariffData = [];
            for ($month = 1; $month < 13; $month++) {
                if (!isset($tariffData[$month])) {
                    $tariffData[$month] = 0;
                }

                foreach ($data as $rawMeter) {
                    foreach ($rawMeter['statistics'] as $rawStatistic) {
                        if (Carbon::createFromTimeString($rawStatistic['date'])->month == $month) {
                            if (isset($rawStatistic[$tariff]) && $type->equals(MeterType::water()) && !$subtype) {
                                $tariffData[$month] += $rawStatistic[$tariff];
                                $total += $rawStatistic[$tariff];
                            } elseif (isset($rawStatistic['d' . $tariff])) {
                                $tariffData[$month] += $rawStatistic['d' . $tariff];
                                $total += $rawStatistic['d' . $tariff];
                            }
                        }
                    }
                }
            }

            $statistics[$tariff] = new MeterStatisticsValue(
                tariff: $tariff,
                total: $total,
                average: $total / 12,
                data: $tariffData,
            );
        }

        return $statistics;
    }

    /**
     * @throws ValidationException
     */
    public function checkMeters(string $accountNumber): array
    {
        return $this->crmClient->checkMeterStatistics($accountNumber);
    }

    /**
     * @throws ValidationException
     */
    public function statisticType(string $accountNumber, string $year): array
    {
        $start_date = Carbon::create($year)->startOfYear()->format('d.m.Y H:i:s');
        $end_date = Carbon::create($year)->endOfYear()->format('d.m.Y H:i:s');

        $data = $this->crmClient->getMeterStatisticsType($accountNumber, $start_date, $end_date);

        return $data;
    }
}
