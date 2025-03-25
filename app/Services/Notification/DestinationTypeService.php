<?php

namespace App\Services\Notification;

use App\Services\Notification\Mappers\Mapper;
use LogicException;
use RuntimeException;

/**
 * Class DestinationTypeService
 *
 * @package App\Services\Notification
 */
class DestinationTypeService
{
    public function __construct(
        private array $typeConfig
    ) {
    }

    public function getByName(string $name): DestinationType
    {
        if (!isset($this->typeConfig[$name])) {
            throw new RuntimeException("Destination type [$name] not found.");
        }

        return $this->mapType($name, $this->typeConfig[$name]);
    }

    /**
     * @return DestinationType[]
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
     * @return DestinationType[]
     */
    public function getAvailableForAdmin(): array
    {
        return array_values(array_filter($this->getAll(), fn(DestinationType $type) => $type->availableForAdmin));
    }

    /**
     * @param DestinationType[] $types
     *
     * @return array
     */
    public function getTypesPayloadParamOptions(array $types): array
    {
        $typesPayloadParamOptions = [];

        foreach ($types as $type) {
            if ($type->hasPayloadParams()) {
                $params = [];
                foreach ($type->payloadParams as $param) {
                    if ($param->hasOptions()) {
                        $params[$param->name] = $this->getPayloadOptionsMap($param);
                    }
                }
                if (!empty($params)) {
                    $typesPayloadParamOptions[$type->name] = $params;
                }
            }
        }

        return $typesPayloadParamOptions;
    }

    public function getPayloadOptionsMap(DestinationTypePayloadParam $param): array
    {
        if (!$param->hasOptions()) {
            return [];
        }

        /** @var Mapper $mapper */
        $mapper = app($param->getOptionsMapper());

        if (!($mapper instanceof Mapper)) {
            throw new LogicException("Bad option mapper class name [{$param->getOptionsMapper()}].");
        }

        return $mapper->getMap();
    }

    private function mapType(string $typeName, array $config): DestinationType
    {
        $params = [];
        foreach ($config['payload_params'] ?? [] as $name => $param) {
            $params[] = new DestinationTypePayloadParam(
                $name,
                $param['label'],
                $param['type'],
                $param['options_mapper'] ?? null,
            );
        }

        return new DestinationType(
            $typeName,
            $config['label'],
            $config['available_for_admin'],
            $config['available_actions'] ?? [],
            $params
        );
    }
}
