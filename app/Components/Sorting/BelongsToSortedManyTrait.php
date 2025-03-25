<?php

namespace App\Components\Sorting;

use Illuminate\Database\Eloquent\Model;

/**
 * Trait BelongsToSortedManyTrait
 *
 * @package App\Components\Sorting
 */
trait BelongsToSortedManyTrait
{
    /**
     * @param string $related
     * @param string $orderColumn
     * @param string $table
     * @param string $foreignPivotKey
     * @param string $relatedPivotKey
     * @param string $parentKey
     * @param string $relatedKey
     * @param string $relation
     *
     * @return BelongsToSortedMany
     */
    public function belongsToSortedMany(
        $related,
        $orderColumn = 'order',
        $table = null,
        $foreignPivotKey = null,
        $relatedPivotKey = null,
        $parentKey = null,
        $relatedKey = null,
        $relation = null
    ) {
        if (is_null($relation)) {
            $relation = $this->guessBelongsToManyRelation();
        }

        $instance = $this->newRelatedInstance($related);
        $foreignPivotKey = $foreignPivotKey ?: $this->getForeignKey();
        $relatedPivotKey = $relatedPivotKey ?: $instance->getForeignKey();

        if (is_null($table)) {
            $table = $this->joiningTable($related, $instance);
        }

        return new BelongsToSortedMany(
            $instance->newQuery(),
            $this,
            $table,
            $foreignPivotKey,
            $relatedPivotKey,
            $parentKey ?: $this->getKeyName(),
            $relatedKey ?: $instance->getKeyName(),
            $relation,
            $orderColumn
        );
    }

    /**
     * @return string
     */
    abstract protected function guessBelongsToManyRelation();

    /**
     * @param string $class
     *
     * @return mixed
     */
    abstract protected function newRelatedInstance($class);

    /**
     * @return string
     */
    abstract public function getForeignKey();

    /**
     * @param string     $related
     * @param Model|null $instance
     *
     * @return string
     */
    abstract public function joiningTable($related, $instance = null);

    /**
     * @return string
     */
    abstract public function getKeyName();

    /**
     * @return string
     */
    abstract protected function getRelations();
}
