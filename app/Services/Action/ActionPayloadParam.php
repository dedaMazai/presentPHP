<?php

namespace App\Services\Action;

/**
 * Class ActionPayloadParam
 *
 * @package App\Services\Action
 */
class ActionPayloadParam
{
    private const TYPE_INTEGER = 'integer';
    private const TYPE_STRING = 'string';

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

    public function isInteger(): bool
    {
        return $this->type === self::TYPE_INTEGER;
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
