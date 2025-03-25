<?php

namespace App\Components\QueryBuilder\Filters;

use Assert\Assert;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

/**
 * Class FiltersEndDate
 *
 * @package App\Components\QueryBuilder\Filters
 */
class FiltersEndDate implements Filter
{
    /**
     * @inheritdoc
     */
    public function __invoke(Builder $query, $value, string $property)
    {
        $query->where('end_date', '<=', $value);
    }
}
