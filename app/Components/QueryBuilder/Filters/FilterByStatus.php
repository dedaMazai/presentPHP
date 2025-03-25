<?php

namespace App\Components\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Spatie\QueryBuilder\Filters\Filter;

/**
 * Class FilterByStatus
 *
 * @package App\Components\QueryBuilder\Filters
 */
class FilterByStatus implements Filter
{
    /**
     * @inheritdoc
     */
    public function __invoke(Builder $query, $value, string $property)
    {
        if ($value) {
            $query->join('ban_phone', 'ban_phone.phone_number', 'users.phone')
                ->addSelect('*', 'ban_phone.id as ban_phone_id', 'users.id as id')
                ->where('unlock_time', '>', Carbon::now()->toDateTimeString());
        }
    }
}
