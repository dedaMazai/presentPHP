<?php

namespace App\Services\Notification\Mappers;

use App\Models\Project\Project;

/**
 * Class ProjectMapper
 *
 * @package App\Services\Notification\Mappers
 */
class ProjectMapper extends Mapper
{
    public function getMap(): array
    {
        return $this->mapCollection(Project::latest()->get(), 'id', 'name');
    }
}
