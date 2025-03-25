<?php

namespace App\Services\Pass;

use App\Models\Pass\Pass;
use App\Models\Pass\PassAssignment;
use App\Models\Pass\PassCarType;
use App\Models\Pass\PassStatus;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\Pass\Dto\SavePassDto;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Class PassRepository
 *
 * @package App\Services\Pass
 */
class PassRepository
{
    public function __construct(private DynamicsCrmClient $dynamicsCrmClient)
    {
    }

    /**
     * @param string $accountNumber
     * @param null $passType
     * @return Collection
     */
    public function getPassesByAccountNumber(string $accountNumber, $passType = null, $sort = null): Collection
    {
        $data = $this->dynamicsCrmClient->getPasses($accountNumber)['passList'];

        $passes = new Collection();

        foreach ($data as $rawPass) {
            if ($passType == 'onetime' && $rawPass['passOptionCode']['code'] != 1) {
                continue;
            } elseif ($passType == 'permanent' && $rawPass['passOptionCode']['code'] != 2) {
                continue;
            }

            try {
                $pass = $this->makePass($rawPass);
            } catch (\Throwable $throwable) {
                continue;
            }

            $passes->add($pass);
        }

        if ($sort == 'by_date') {
            $passes = $passes->sortByDesc(function ($obj, $key) {
                return $obj->getCreationDate()?->timestamp;
            });
        }

        return $passes->values();
    }

    public function cancelPass(string $passId)
    {
        $this->dynamicsCrmClient->cancelPass($passId);

        return null;
    }

    public function storePass(string $accountNumber, SavePassDto $dto)
    {
        $this->dynamicsCrmClient->storePass($accountNumber, $dto);

        return null;
    }

    private function makePass(array $data): Pass
    {
        return new Pass(
            id: $data['id'],
            name: $data['passTypeCode']['code'] == 1?$data['carNumber']:$data['fullNameGuest'],
            status: isset($data['passStatus']['code'])?PassStatus::from($data['passStatus']['code']):
                PassStatus::from('cancelled'),
            arrivalDate: isset($data['arrivalDate'])?new Carbon($data['arrivalDate']):null,
            startDate: isset($data['passStartDate'])?new Carbon($data['passStartDate']):null,
            endDate: isset($data['passEndDate'])?new Carbon($data['passEndDate']):null,
            creationDate: isset($data['createdOnDate'])?new Carbon($data['createdOnDate']):null,
            comment: $data['description'] ?? null,
            assignment: isset($data['passTypeCode']['code'])?PassAssignment::from($data['passTypeCode']['code']):null,
            carType: isset($data['carTypeCode']['code'])?PassCarType::from($data['carTypeCode']['code']):null,
        );
    }
}
