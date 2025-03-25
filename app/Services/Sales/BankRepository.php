<?php

namespace App\Services\Sales;

use App\Models\Sales\Bank\Bank;
use App\Models\Sales\Bank\BankInfo;
use App\Models\Sales\Bank\BankType;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;

/**
 * Class BankRepository
 *
 * @package App\Services\Sales
 */
class BankRepository
{
    public function __construct(private DynamicsCrmClient $dynamicsCrmClient,)
    {
    }

    /**
     * @return Bank[]
     * @throws BadRequestException
     */
    public function getAllByAddressId(string $addressId, ?BankType $type): array
    {
        $data = $this->dynamicsCrmClient->getBanksByAddressId($addressId);

        $banks = [];
        foreach ($data['accountList'] as $bank) {
            if ($type && !$type->equals(BankType::from($bank['accreditationType']['code']))) {
                continue;
            }

            /** @var BankInfo $bankInfo */
            $bankInfo = BankInfo::byCrmId($bank['id'])->first();
            if ($bankInfo) {
                $banks[] = $this->makeBank($bank, $bankInfo);
            }
        }

        return $banks;
    }

    public function makeBank(array $data, ?BankInfo $bankInfo): Bank
    {
        return new Bank(
            id: $data['id'],
            name: $data['name'],
            nameShort: $data['nameShot'] ?? null,
            nameFull: $data['nameFull'] ?? null,
            type: BankType::from($data['accreditationType']['code']),
            appendInfo: $bankInfo,
        );
    }
}
