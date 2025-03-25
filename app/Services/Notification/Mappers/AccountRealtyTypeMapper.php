<?php

namespace App\Services\Notification\Mappers;

use App\Models\Account\AccountRealtyType;
use App\Models\GroupingRealityTypes;

/**
 * Class AccountRealtyTypeMapper
 *
 * @package App\Services\Notification\Mappers
 */
class AccountRealtyTypeMapper extends Mapper
{
    public function getMap(): array
    {
        return collect(GroupingRealityTypes::all())
            ->map(fn(GroupingRealityTypes $type) => ['key' => $type->group_reality_ids, 'label' => $type->group_reality_name])
            ->toArray();
    }
}
