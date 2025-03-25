<?php

namespace App\Models\Sales\Property;

/**
 * Class PropertyImage
 *
 * @package App\Models\Sales\Property
 */
class ObjectPlan
{
    public function __construct(
        private ?string $common,
        private ?string $object,
        private ?string $floor,
    ) {
    }

    /**
     * @return string|null
     */
    public function getCommon(): ?string
    {
        return $this->common;
    }

    /**
     * @return string|null
     */
    public function getObject(): ?string
    {
        return $this->object;
    }

    /**
     * @return string|null
     */
    public function getFloor(): ?string
    {
        return $this->floor;
    }
}
