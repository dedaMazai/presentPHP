<?php

namespace App\Components\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Spatie\QueryBuilder\Filters\Filter;

/**
 * Class FilterByActive
 *
 * @package App\Components\QueryBuilder\Filters
 */
class FilterByActive implements Filter
{
    /**
     * @inheritdoc
     */
    public function __invoke(Builder $query, $value, string $property)
    {
        $query->where('unlock_time', '>', Carbon::now()->toDateTimeString());
    }
}
