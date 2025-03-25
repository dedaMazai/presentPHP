<?php

namespace App\Services\Notification\Mappers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Mapper
 *
 * @package App\Services\Notification\Mappers
 */
abstract class Mapper
{
    abstract public function getMap(): array;

    protected function mapCollection(Collection $collection, string $keyName, string $valueName): array
    {
        return $collection->map(function (Model $model) use ($keyName, $valueName) {
            return [
                'key' => $model->$keyName,
                'label' => $model->$valueName,
            ];
        })->toArray();
    }
}
