<?php

namespace App\Components\Sorting;

use Illuminate\Database\Query\Builder;

/**
 * Trait ToSortedManyTrait
 *
 * @package App\Components\Sorting
 */
trait ToSortedManyTrait
{
    protected string $orderColumn;

    public function setNewOrder(mixed $ids): void
    {
        $order = 1;
        foreach ($ids as $id) {
            $this->updateExistingPivot($id, [$this->orderColumn => $order++]);
        }
    }

    /**
     * @param mixed $id
     * @param array $attributes
     * @param bool  $touch
     *
     * @return void
     */
    public function attach($id, array $attributes = [], $touch = true)
    {
        $attributes[$this->orderColumn] = $this->getNextOrderNumber();

        parent::attach($id, $attributes, $touch);
    }

    protected function getNextOrderNumber(): int
    {
        return $this->newPivotQuery()->max($this->orderColumn) + 1;
    }

    /**
     * @return Builder
     */
    abstract protected function newPivotQuery();

    /**
     * @param mixed $ids
     * @param bool  $touch
     *
     * @return int
     */
    abstract public function detach($ids = null, $touch = true);

    protected function setOrderColumn(string $orderColumn): void
    {
        $this->orderColumn = $orderColumn;
        $this->withPivot($orderColumn);
        $this->orderByPivot($orderColumn);
    }

    /**
     * @param array|mixed $columns
     *
     * @return $this
     */
    abstract public function withPivot($columns);
}
