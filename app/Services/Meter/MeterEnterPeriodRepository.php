<?php

namespace App\Services\Meter;

use App\Models\Meter\MeterEnterPeriod;
use App\Services\Crm\CrmClient;
use Carbon\Carbon;
use Exception;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class MeterEnterPeriodRepository
 *
 * @package App\Services\Meter
 */
class MeterEnterPeriodRepository
{
    public function __construct(
        private CrmClient $crmClient,
        private CacheInterface $cache,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getByBuildingAddressId(string $buildingAddressId): ?MeterEnterPeriod
    {
        $meterEnterPeriod = $this->cache->get($this->key($buildingAddressId));
        if ($meterEnterPeriod !== null) {
            return $meterEnterPeriod;
        }

        try {
            $data = $this->crmClient->getMeterEnterPeriod($buildingAddressId);
        } catch (Exception) {
            return null;
        }
        $meterEnterPeriod = $this->makeMeterEnterPeriod($data);

        $this->cacheMeterEnterPeriod($buildingAddressId, $meterEnterPeriod);

        return $meterEnterPeriod;
    }

    private function key(string $buildingAddressId): string
    {
        return "meters.periods.{$buildingAddressId}";
    }

    private function makeMeterEnterPeriod(array $data): MeterEnterPeriod
    {
        return new MeterEnterPeriod(
            startDate: new Carbon($data['start_date']),
            endDate: new Carbon($data['end_date']),
        );
    }

    /**
     * @throws InvalidArgumentException
     */
    private function cacheMeterEnterPeriod(string $buildingAddressId, MeterEnterPeriod $meterEnterPeriod): void
    {
        $this->cache->set($this->key($buildingAddressId), $meterEnterPeriod, now()->addHour());
    }
}
