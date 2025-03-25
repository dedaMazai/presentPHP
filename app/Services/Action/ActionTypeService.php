<?php

namespace App\Services\Action;

use App\Services\Action\Mappers\Mapper;
use LogicException;

/**
 * Class ActionTypeService
 *
 * @package App\Services\Action
 */
class ActionTypeService
{
    public function __construct(
        private array $typeConfig
    ) {
    }

    /**
     * @return ActionType[]
     */
    public function getAll(): array
    {
        $types = [];

        foreach ($this->typeConfig as $name => $config) {
            $types[] = $this->mapType($name, $config);
        }

        return $types;
    }

    /**
     * @param ActionType[] $types
     *
     * @return array
     */
    public function getPayloadMapTypes(array $types): array
    {
        $map = [];

        foreach ($types as $type) {
            if ($type->hasParams()) {
                $params = [];
                foreach ($type->payloadParams as $param) {
                    if ($param->hasOptions()) {
                        $params[$param->name] = $this->getPayloadOptionsMap($param);
                    }
                }
                if (!empty($params)) {
                    $map[$type->name] = $params;
                }
            }
        }

        return $map;
    }

    public function getByName(string $name): ?ActionType
    {
        if (!isset($this->typeConfig[$name])) {
            return null;
        }

        return $this->mapType($name, $this->typeConfig[$name]);
    }

    public function getPayloadOptionsMap(ActionPayloadParam $param): array
    {
        if (!$param->hasOptions()) {
            return [];
        }

        /** @var Mapper $mapper */
        $mapper = app($param->getOptionsMapper());

        if (!($mapper instanceof Mapper)) {
            throw new LogicException("Bad mapper class name: {$param->getOptionsMapper()}");
        }

        return $mapper->getMap();
    }

    private function mapType(string $typeName, array $config): ActionType
    {
        $params = [];

        foreach ($config['payload_params'] ?? [] as $name => $param) {
            $params[] = new ActionPayloadParam(
                $name,
                $param['label'],
                $param['type'],
                $param['options_mapper'] ?? null
            );
        }

        return new ActionType($typeName, $config['label'], $params);
    }
}
