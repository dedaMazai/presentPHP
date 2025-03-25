<?php

namespace App\Components\Sorting;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class BelongsToSortedMany
 *
 * @package App\Components\Sorting
 */
class BelongsToSortedMany extends BelongsToMany
{
    use ToSortedManyTrait;

    /**
     * BelongsToSortedMany constructor.
     *
     * @param Builder     $query
     * @param Model       $parent
     * @param string      $table
     * @param string      $foreignPivotKey
     * @param string      $relatedPivotKey
     * @param string      $parentKey
     * @param string      $relatedKey
     * @param string|null $relationName
     * @param string|null $orderColumn
     */
    public function __construct(
        Builder $query,
        Model $parent,
        $table,
        $foreignPivotKey,
        $relatedPivotKey,
        $parentKey,
        $relatedKey,
        $relationName = null,
        $orderColumn = null
    ) {
        parent::__construct(
            $query,
            $parent,
            $table,
            $foreignPivotKey,
            $relatedPivotKey,
            $parentKey,
            $relatedKey,
            $relationName = null
        );

        $this->setOrderColumn($orderColumn);
    }

    /**
     * @return string
     */
    public function getRelatedKey()
    {
        return $this->relatedPivotKey;
    }

    /**
     * @return string
     */
    public function getForeignKey()
    {
        return $this->foreignPivotKey;
    }
}
