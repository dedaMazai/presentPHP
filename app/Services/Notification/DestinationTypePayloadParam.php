<?php

namespace App\Services\Notification;

/**
 * Class DestinationTypePayloadParam
 *
 * @package App\Services\Notification
 */
class DestinationTypePayloadParam
{
    private const TYPE_STRING = 'string';
    private const TYPE_ARRAY = 'array';

    public function __construct(
        public string $name,
        public string $label,
        public string $type,
        public ?string $optionsMapper = null,
    ) {
    }

    public function isString(): bool
    {
        return $this->type === self::TYPE_STRING;
    }

    public function isArray(): bool
    {
        return $this->type === self::TYPE_ARRAY;
    }

    public function hasOptions(): bool
    {
        return $this->optionsMapper !== null;
    }

    public function getOptionsMapper(): ?string
    {
        return $this->optionsMapper;
    }
}
