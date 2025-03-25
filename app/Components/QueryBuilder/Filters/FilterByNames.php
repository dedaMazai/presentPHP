<?php

namespace App\Components\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Spatie\QueryBuilder\Filters\Filter;

/**
 * Class FilterByNames
 *
 * @package App\Components\QueryBuilder\Filters
 */
class FilterByNames implements Filter
{
    /**
     * @inheritdoc
     */
    public function __invoke(Builder $query, $value, string $property)
    {
        if ($value) {
            $names = explode(' ', trim($value));

            if (count($names) > 0) {
                foreach ($names as $name) {
                    $query->orWhere(function ($q) use ($name) {
                        $q->whereRaw('LOWER(first_name) LIKE LOWER(?)', ['%' . $name . '%'])
                            ->orWhereRaw('LOWER(last_name) LIKE LOWER(?)', ['%' . $name . '%'])
                            ->orWhereRaw('LOWER(middle_name) LIKE LOWER(?)', ['%' . $name . '%']);
                    });
                }
            }
        }
    }
}
