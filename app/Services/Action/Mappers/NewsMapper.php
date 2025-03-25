<?php

namespace App\Services\Action\Mappers;

use App\Models\News\News;

/**
 * Class NewsMapper
 *
 * @package App\Services\Action\Mappers
 */
class NewsMapper extends Mapper
{
    public function getMap(): array
    {
        return $this->mapCollection(News::latest()->get(), 'id', 'title');
    }
}
