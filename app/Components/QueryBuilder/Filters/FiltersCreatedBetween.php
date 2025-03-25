<?php

namespace App\Components\QueryBuilder\Filters;

use Assert\Assert;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

/**
 * Class FiltersCreatedBetween
 *
 * @package App\Components\QueryBuilder\Filters
 */
class FiltersCreatedBetween implements Filter
{
    /**
     * @inheritdoc
     */
    public function __invoke(Builder $query, $value, string $property)
    {
        Assert::that($value)->isArray()->count(2);

        $query->where('created_at', '>=', $value[0])
            ->where('created_at', '<=', $value[1]);
    }
}
