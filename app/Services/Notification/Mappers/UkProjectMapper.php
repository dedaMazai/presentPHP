<?php

namespace App\Services\Notification\Mappers;

use App\Models\UkProject;

/**
 * Class UkProjectMapper
 *
 * @package App\Services\Notification\Mappers
 */
class UkProjectMapper extends Mapper
{
    public function getMap(): array
    {
        return $this->mapCollection(UkProject::latest()->get(), 'id', 'name');
    }
}
