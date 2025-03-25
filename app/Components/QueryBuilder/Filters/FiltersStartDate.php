<?php

namespace App\Components\QueryBuilder\Filters;

use Assert\Assert;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

/**
 * Class FiltersStartDate
 *
 * @package App\Components\QueryBuilder\Filters
 */
class FiltersStartDate implements Filter
{
    /**
     * @inheritdoc
     */
    public function __invoke(Builder $query, $value, string $property)
    {
        $query->where('start_date', '>=', $value);
    }
}
