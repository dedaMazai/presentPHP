<?php

namespace App\Services\Action\Mappers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class Mapper
 *
 * @package App\Services\Action\Mappers
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
